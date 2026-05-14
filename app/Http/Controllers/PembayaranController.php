<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use App\Models\Tagihan;
use App\Models\Pembayaran;



class PembayaranController extends Controller
{
    // Halaman kasir
    public function index()
    {
        $tagihan = Tagihan::where('status', 'belum_lunas')->with('pelanggan')->get();
        return view('pembayaran.index', compact('tagihan'));
    }

    // Bayar tunai (manual)
    public function bayarTunai(Request $request)
    {
        $tagihan = Tagihan::findOrFail($request->tagihan_id);

        Pembayaran::create([
            'tagihan_id'      => $tagihan->id,
            'jumlah_bayar'    => $tagihan->total_tagihan,
            'metode'          => 'tunai',
            'status'          => 'lunas',
            'tanggal_bayar'   => now(),
        ]);

        $tagihan->update(['status' => 'lunas']);

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran tunai berhasil!'
        ]);
    }

    // Bayar via Midtrans (QRIS, Transfer, E-Wallet)
    public function bayarMidtrans(Request $request)
    {
        $tagihan = Tagihan::findOrFail($request->tagihan_id);

        $params = [
            'transaction_details' => [
                'order_id'     => 'PAM-' . $tagihan->id . '-' . time(),
                'gross_amount' => $tagihan->total_tagihan,
            ],
            'customer_details' => [
                'first_name' => $tagihan->pelanggan->nama,
                'phone'      => $tagihan->pelanggan->no_hp,
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        return response()->json([
            'success'    => true,
            'snap_token' => $snapToken,
            'client_key' => config('services.midtrans.client_key'),
        ]);
    }

    // Notifikasi otomatis dari Midtrans
    public function notifikasi(Request $request)
    {
        $notif = new \Midtrans\Notification();
        $status = $notif->transaction_status;
        $orderId = $notif->order_id;

        $tagihanId = explode('-', $orderId)[1];
        $tagihan = Tagihan::find($tagihanId);

        if (in_array($status, ['capture', 'settlement'])) {
            $tagihan->update(['status' => 'lunas']);
            Pembayaran::create([
                'tagihan_id'    => $tagihan->id,
                'jumlah_bayar'  => $tagihan->total_tagihan,
                'metode'        => 'midtrans',
                'status'        => 'lunas',
                'tanggal_bayar' => now(),
            ]);
        }

        return response()->json(['success' => true]);
    }
}