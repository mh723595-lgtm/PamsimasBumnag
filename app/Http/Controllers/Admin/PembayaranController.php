<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AktivitasLog;
use App\Models\Notifikasi;
use App\Models\PakasirTransaction;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\TagihanAir;
use App\Services\PakasirService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PembayaranController extends Controller
{
    // Tidak ada __construct Midtrans — dihapus total.

    /**
     * Daftar tagihan belum bayar + statistik pembayaran.
     */
    public function index(): View
    {
        $pelanggan = Pelanggan::select('id', 'nama_pelanggan', 'nomor_pelanggan')
            ->orderBy('nama_pelanggan')
            ->get();

        $tagihan = TagihanAir::whereIn('status', ['belum_bayar', 'terlambat'])
            ->with('pelanggan')
            ->get();

        $stats = [
            'pending'    => TagihanAir::whereIn('status', ['belum_bayar', 'terlambat'])->count(),
            'konfirmasi' => Pembayaran::where('status', 'konfirmasi')->count(),
            'ditolak'    => Pembayaran::where('status', 'ditolak')->count(),
            'total'      => Pembayaran::where('status', 'konfirmasi')->sum('jumlah_bayar'),
        ];

        return view('admin.pembayaran.index', compact('tagihan', 'stats', 'pelanggan'));
    }

    /**
     * Ambil daftar tagihan pelanggan tertentu (AJAX).
     */
    public function tagihanPelanggan(Pelanggan $pelanggan): JsonResponse
    {
        $tagihan = TagihanAir::where('pelanggan_id', $pelanggan->id)
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get(['id', 'bulan', 'tahun', 'total_tagihan', 'total_bayar', 'status', 'nomor_tagihan', 'denda']);

        return response()->json(['pelanggan' => $pelanggan, 'tagihan' => $tagihan]);
    }

    /**
     * Proses pembayaran tunai langsung (kasir).
     */
    public function bayarTunai(Request $request): JsonResponse
    {
        $tagihan = TagihanAir::findOrFail($request->tagihan_id);

        Pembayaran::create([
            'nomor_pembayaran'  => 'PAY-' . date('Ymd') . '-' . str_pad($tagihan->id, 4, '0', STR_PAD_LEFT),
            'tagihan_id'        => $tagihan->id,
            'pelanggan_id'      => $tagihan->pelanggan_id,
            'jumlah_bayar'      => $tagihan->total_bayar ?: $tagihan->total_tagihan,
            'metode_bayar'      => 'tunai',
            'status'            => 'konfirmasi',
            'tanggal_bayar'     => now(),
            'dikonfirmasi_oleh' => Auth::id(),
        ]);

        $tagihan->update(['status' => 'lunas']);

        return response()->json(['success' => true, 'message' => 'Pembayaran tunai berhasil!']);
    }

    /**
     * Buat link pembayaran Pakasir (AJAX — dipanggil dari kasir dashboard).
     */
    public function bayarPakasir(Request $request): JsonResponse
    {
        $request->validate([
            'tagihan_id' => 'required|integer|exists:tagihan_air,id',
        ]);

        $tagihan = TagihanAir::with('pelanggan')->findOrFail($request->tagihan_id);

        if ($tagihan->isLunas()) {
            return response()->json([
                'success' => false,
                'message' => 'Tagihan ini sudah berstatus lunas.',
            ], 422);
        }

        // Jika sudah ada PakasirTransaction pending yang masih aktif, kembalikan link yang ada
        $existing = PakasirTransaction::where('tagihan_id', $tagihan->id)
            ->where('status', 'pending')
            ->where(function ($q) {
                $q->whereNull('expired_at')
                  ->orWhere('expired_at', '>', now());
            })
            ->latest()
            ->first();

        if ($existing && $existing->payment_url) {
            return response()->json([
                'success'        => true,
                'payment_url'    => $existing->payment_url,
                'transaction_id' => $existing->pakasir_transaction_id ?? $existing->merchant_ref,
                'merchant_ref'   => $existing->merchant_ref,
                'message'        => 'Menggunakan link pembayaran yang sudah ada.',
            ]);
        }

        $pakasirService = app(PakasirService::class);
        $amount         = (int) round($tagihan->total_bayar ?: $tagihan->total_tagihan);

        $result = $pakasirService->createTransaction([
            'amount'        => $amount,
            'merchant_ref'  => $tagihan->nomor_tagihan,
            'customer_name' => $tagihan->pelanggan->nama_pelanggan ?? 'Pelanggan',
            'customer_phone'=> $tagihan->pelanggan->no_hp ?? '',
            'description'   => 'Tagihan Air PAMSIMAS - ' . $tagihan->periodeTeks(),
            'success_url'   => route('pelanggan.pakasir.sukses') . '?ref=' . $tagihan->nomor_tagihan,
            'failed_url'    => route('pelanggan.pakasir.gagal')  . '?ref=' . $tagihan->nomor_tagihan,
        ]);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 500);
        }

        $data     = $result['data'];
        $pakasirTx = PakasirTransaction::create([
            'tagihan_id'             => $tagihan->id,
            'pelanggan_id'           => $tagihan->pelanggan_id,
            'pakasir_transaction_id' => $data['transaction_id'] ?? $data['id'] ?? null,
            'merchant_ref'           => $tagihan->nomor_tagihan,
            'amount'                 => $amount,
            'status'                 => 'pending',
            'payment_url'            => $data['payment_url'] ?? null,
            'qris_url'               => $data['qris_url']    ?? null,
            'expired_at'             => isset($data['expired_at'])
                                            ? \Carbon\Carbon::parse($data['expired_at'])
                                            : now()->addMinutes(config('pakasir.expiry_minutes', 60)),
            'raw_response'           => $data,
        ]);

        AktivitasLog::catat(
            'buat_transaksi_pakasir',
            "Admin buat link Pakasir untuk tagihan {$tagihan->nomor_tagihan}",
            'TagihanAir',
            $tagihan->id
        );

        return response()->json([
            'success'        => true,
            'payment_url'    => $data['payment_url'],
            'transaction_id' => $pakasirTx->pakasir_transaction_id ?? $pakasirTx->merchant_ref,
            'merchant_ref'   => $tagihan->nomor_tagihan,
            'message'        => 'Link pembayaran Pakasir berhasil dibuat.',
        ]);
    }

    /**
     * Konfirmasi pembayaran manual oleh admin.
     */
    public function konfirmasi(Request $request, TagihanAir $tagihan): RedirectResponse
    {
        $request->validate(['catatan' => 'nullable|string|max:500']);

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

        AktivitasLog::catat(
            'konfirmasi_pembayaran',
            "Konfirmasi tagihan {$tagihan->nomor_tagihan}",
            'TagihanAir',
            $tagihan->id
        );

        return back()->with('success', 'Pembayaran berhasil dikonfirmasi.');
    }

    /**
     * Detail pembayaran.
     */
    public function show(Pembayaran $pembayaran): View
    {
        $pembayaran->load(['tagihan', 'pelanggan']);
        return view('admin.pembayaran.show', compact('pembayaran'));
    }

    /**
     * Struk pembayaran — load relasi pakasirTransaction juga.
     */
    public function struk(Pembayaran $pembayaran): View
    {
        $pembayaran->load(['tagihan.pelanggan', 'pelanggan', 'pakasirTransaction']);
        return view('admin.pembayaran.struk', compact('pembayaran'));
    }
}