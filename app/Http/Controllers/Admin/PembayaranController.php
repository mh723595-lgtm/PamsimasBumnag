<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TagihanAir;
use App\Models\Pembayaran;
use App\Models\Pelanggan;
use App\Models\Notifikasi;
use App\Models\AktivitasLog;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Facades\Auth;

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
        $pelanggan = Pelanggan::select('id', 'nama_pelanggan', 'nomor_pelanggan')->orderBy('nama_pelanggan')->get();

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
            ->get(['id', 'bulan', 'tahun', 'total_tagihan', 'total_bayar', 'status', 'nomor_tagihan', 'denda']);

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
            'jumlah_bayar'     => $tagihan->total_bayar ?: $tagihan->total_tagihan,
            'metode_bayar'     => 'tunai',
            'status'           => 'konfirmasi',
            'tanggal_bayar'    => now(),
            'dikonfirmasi_oleh' => Auth::id(),
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
                'metode_bayar'           => 'transfer',
                'status'           => 'konfirmasi',
                'tanggal_bayar'    => now(),
                'dikonfirmasi_oleh' => Auth::id(),
            ]);
        }

        return response()->json(['success' => true]);
    }

    // Tambahkan method baru:
    public function konfirmasi(Request $request, TagihanAir $tagihan)
    {
        $request->validate([
            'catatan' => 'nullable|string|max:500',
        ]);

        // Buat atau update record pembayaran
        $pembayaran = Pembayaran::where('tagihan_id', $tagihan->id)->first();

        if ($pembayaran) {
            $pembayaran->update([
                'status'            => 'konfirmasi',
                'dikonfirmasi_oleh' => Auth::id(),
                'catatan'           => $request->catatan,
            ]);
        } else {
            $seq = Pembayaran::whereDate('created_at', today())->count() + 1;
            Pembayaran::create([
                'tagihan_id'        => $tagihan->id,
                'pelanggan_id'      => $tagihan->pelanggan_id,
                'nomor_pembayaran'  => 'PAY-' . now()->format('Ymd') . '-' . str_pad($seq, 5, '0', STR_PAD_LEFT),
                'jumlah_bayar'      => $tagihan->total_bayar ?: $tagihan->total_tagihan,
                'tanggal_bayar'     => now()->toDateString(),
                'metode_bayar'      => 'tunai',
                'status'            => 'konfirmasi',
                'dikonfirmasi_oleh' => Auth::id(),
                'catatan'           => $request->catatan,
            ]);
        }

        $tagihan->update(['status' => 'lunas']);

        if ($tagihan->pelanggan?->user_id) {
            Notifikasi::kirim(
                $tagihan->pelanggan->user_id,
                '✅ Pembayaran Dikonfirmasi',
                "Pembayaran tagihan {$tagihan->nomor_tagihan} telah dikonfirmasi.",
                'success'
            );
        }

        AktivitasLog::catat('konfirmasi_pembayaran', "Konfirmasi tagihan {$tagihan->nomor_tagihan}", 'TagihanAir', $tagihan->id);

        return back()->with('success', 'Pembayaran berhasil dikonfirmasi.');
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
