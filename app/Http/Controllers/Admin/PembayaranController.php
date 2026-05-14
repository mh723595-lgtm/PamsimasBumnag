<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TagihanAir;
use App\Models\Pembayaran;
use App\Models\Pelanggan;
use Midtrans\Config;
use Midtrans\Snap;

class PembayaranController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function index()
    {
        $pelanggan = Pelanggan::select('id','nama_pelanggan','nomor_pelanggan')->orderBy('nama_pelanggan')->get();

        $tagihan = TagihanAir::whereIn('status', ['belum_bayar', 'terlambat'])->with('pelanggan')->get();

        $stats = [
            'pending'    => TagihanAir::whereIn('status', ['belum_bayar', 'terlambat'])->count(),
            'konfirmasi' => Pembayaran::where('status', 'konfirmasi')->count(),
            'ditolak'    => Pembayaran::where('status', 'ditolak')->count(),
            'total'      => Pembayaran::where('status', 'konfirmasi')->sum('jumlah_bayar'),
        ];

        return view('admin.pembayaran.index', compact('tagihan', 'stats', 'pelanggan'));
    }

    public function tagihanPelanggan(Pelanggan $pelanggan)
    {
        $tagihan = TagihanAir::where('pelanggan_id', $pelanggan->id)
            ->orderBy('tahun')->orderBy('bulan')
            ->get(['id','bulan','tahun','total_tagihan','total_bayar','status','nomor_tagihan','denda']);

        return response()->json([
            'pelanggan' => $pelanggan,
            'tagihan'   => $tagihan,
        ]);
    }

    public function bayarTunai(Request $request)
    {
        $tagihan = TagihanAir::findOrFail($request->tagihan_id);

        Pembayaran::create([
            'nomor_pembayaran' => 'PAY-' . date('Ymd') . '-' . str_pad($tagihan->id, 4, '0', STR_PAD_LEFT),
            'tagihan_id'       => $tagihan->id,
            'pelanggan_id'     => $tagihan->pelanggan_id,
            'jumlah_bayar'     => $tagihan->total_tagihan,
            'metode'           => 'tunai',
            'status'           => 'konfirmasi',
            'tanggal_bayar'    => now(),
        ]);

        $tagihan->update(['status' => 'lunas']);

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran tunai berhasil!'
        ]);
    }

    public function bayarMidtrans(Request $request)
    {
        $tagihan = TagihanAir::findOrFail($request->tagihan_id);

        $params = [
            'transaction_details' => [
                'order_id'     => 'PAM-' . $tagihan->id . '-' . time(),
                'gross_amount' => (int) $tagihan->total_bayar,
            ],
            'customer_details' => [
                'first_name' => $tagihan->pelanggan->nama_pelanggan,
                'phone'      => $tagihan->pelanggan->no_hp ?? '08000000000',
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        return response()->json([
            'success'    => true,
            'snap_token' => $snapToken,
            'client_key' => config('services.midtrans.client_key'),
        ]);
    }

    public function notifikasi(Request $request)
    {
        $notif     = new \Midtrans\Notification();
        $status    = $notif->transaction_status;
        $orderId   = $notif->order_id;

        $tagihanId = explode('-', $orderId)[1];
        $tagihan   = TagihanAir::find($tagihanId);

        if ($tagihan && in_array($status, ['capture', 'settlement'])) {
            $tagihan->update(['status' => 'lunas']);
            Pembayaran::create([
                'nomor_pembayaran' => 'PAY-' . date('Ymd') . '-' . str_pad($tagihan->id, 4, '0', STR_PAD_LEFT),
                'tagihan_id'       => $tagihan->id,
                'pelanggan_id'     => $tagihan->pelanggan_id,
                'jumlah_bayar'     => $tagihan->total_bayar,
                'metode'           => 'midtrans',
                'status'           => 'konfirmasi',
                'tanggal_bayar'    => now(),
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function show(Pembayaran $pembayaran)
    {
        $pembayaran->load(['tagihan', 'pelanggan']);
        return view('admin.pembayaran.show', compact('pembayaran'));
    }

    public function struk(Pembayaran $pembayaran)
    {
        $pembayaran->load(['tagihan.pelanggan', 'pelanggan']);
        return view('admin.pembayaran.struk', compact('pembayaran'));
    }
}