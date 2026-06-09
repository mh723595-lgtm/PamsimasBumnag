<?php

namespace App\Http\Controllers;

use App\Models\AktivitasLog;
use App\Models\Notifikasi;
use App\Models\PakasirTransaction;
use App\Models\Pembayaran;
use App\Models\TagihanAir;
use App\Services\PakasirService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PakasirController extends Controller
{
    public function __construct(protected PakasirService $pakasirService)
    {
    }

    // ──────────────────────────────────────────────────────────────
    // PELANGGAN: Self-service bayar tagihan
    // ──────────────────────────────────────────────────────────────

    /**
     * Pelanggan klik "Bayar Sekarang" → buat transaksi Pakasir → redirect ke payment_url
     */
    public function bayar(Request $request, TagihanAir $tagihan): RedirectResponse
    {
        $user      = Auth::user();
        $pelanggan = $user->pelanggan;

        if (!$pelanggan) {
            return redirect()->route('pelanggan.tagihan.index')
                ->with('error', 'Data pelanggan tidak ditemukan.');
        }

        // Ownership check: pelanggan hanya boleh akses tagihan miliknya
        if ((int) $tagihan->pelanggan_id !== (int) $pelanggan->id) {
            abort(403, 'Anda tidak memiliki akses ke tagihan ini.');
        }

        // Sudah lunas
        if ($tagihan->isLunas()) {
            return redirect()->route('pelanggan.tagihan.show', $tagihan)
                ->with('info', 'Tagihan ini sudah lunas.');
        }

        // Cek apakah sudah ada transaksi Pakasir pending yang masih aktif
        $existing = PakasirTransaction::where('tagihan_id', $tagihan->id)
            ->where('status', 'pending')
            ->where(function ($q) {
                $q->whereNull('expired_at')
                  ->orWhere('expired_at', '>', now());
            })
            ->latest()
            ->first();

        if ($existing && $existing->payment_url) {
            return redirect($existing->payment_url);
        }

        // Buat transaksi baru
        $tagihan->load('pelanggan');
        $amount = (int) round($tagihan->total_bayar ?: $tagihan->total_tagihan);

        $result = $this->pakasirService->createTransaction([
            'amount'        => $amount,
            'merchant_ref'  => $tagihan->nomor_tagihan,
            'customer_name' => $tagihan->pelanggan->nama_pelanggan,
            'customer_phone'=> $tagihan->pelanggan->no_hp ?? '',
            'description'   => 'Tagihan Air PAMSIMAS - ' . $tagihan->periodeTeks(),
            'success_url'   => route('pelanggan.pakasir.sukses') . '?ref=' . $tagihan->nomor_tagihan,
            'failed_url'    => route('pelanggan.pakasir.gagal')  . '?ref=' . $tagihan->nomor_tagihan,
        ]);

        if (!$result['success']) {
            Log::error('[PakasirController] bayar gagal', [
                'tagihan_id' => $tagihan->id,
                'message'    => $result['message'],
            ]);

            return redirect()->route('pelanggan.tagihan.show', $tagihan)
                ->with('error', 'Gagal membuat transaksi pembayaran: ' . $result['message']);
        }

        $data = $result['data'];

        PakasirTransaction::create([
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

        return redirect($data['payment_url']);
    }

    // ──────────────────────────────────────────────────────────────
    // ADMIN: Buat transaksi via AJAX (dipanggil dari PembayaranController)
    // ──────────────────────────────────────────────────────────────

    /**
     * Admin kasir: buat transaksi Pakasir untuk tagihan yang dipilih.
     * Dipanggil via AJAX POST.
     */
    public function bayarAdmin(Request $request): JsonResponse
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

        // Cek existing pending transaction yang masih aktif
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

        $amount = (int) round($tagihan->total_bayar ?: $tagihan->total_tagihan);

        $result = $this->pakasirService->createTransaction([
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

        $data = $result['data'];

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
            "Buat link Pakasir untuk tagihan {$tagihan->nomor_tagihan}",
            'TagihanAir',
            $tagihan->id
        );

        return response()->json([
            'success'        => true,
            'payment_url'    => $data['payment_url'],
            'transaction_id' => $pakasirTx->pakasir_transaction_id ?? $pakasirTx->merchant_ref,
            'merchant_ref'   => $tagihan->nomor_tagihan,
            'message'        => 'Link pembayaran berhasil dibuat.',
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    // Polling status (AJAX) — baca dari DB, tidak hit API setiap kali
    // ──────────────────────────────────────────────────────────────

    /**
     * Cek status transaksi berdasarkan merchant_ref (= nomor_tagihan).
     * Digunakan untuk polling di frontend.
     */
    public function cekStatus(Request $request, string $ref): JsonResponse
    {
        $tx = PakasirTransaction::where('merchant_ref', $ref)
            ->latest()
            ->first();

        if (!$tx) {
            return response()->json([
                'found'  => false,
                'status' => null,
                'message'=> 'Transaksi tidak ditemukan.',
            ], 404);
        }

        // Jika masih pending, sync sekali ke API Pakasir (bukan setiap poll)
        if ($tx->isPending() && $tx->pakasir_transaction_id && $tx->updated_at->diffInSeconds(now()) > 30) {
            $apiResult = $this->pakasirService->checkStatus($tx->pakasir_transaction_id);
            if ($apiResult['success']) {
                $apiStatus  = $apiResult['data']['status'] ?? 'pending';
                $mappedStatus = $this->pakasirService->mapStatus($apiStatus);
                if ($mappedStatus !== 'pending') {
                    $tx->update(['status' => $mappedStatus]);
                    $tx->refresh();
                }
            }
        }

        // Cek expired berdasarkan waktu
        if ($tx->isPending() && $tx->expired_at && $tx->expired_at->isPast()) {
            $tx->update(['status' => 'expired']);
            $tx->refresh();
        }

        return response()->json([
            'found'          => true,
            'status'         => $tx->status,
            'is_paid'        => $tx->isPaid(),
            'is_pending'     => $tx->isPending(),
            'is_expired'     => $tx->isExpired(),
            'is_failed'      => $tx->isFailed(),
            'merchant_ref'   => $tx->merchant_ref,
            'payment_url'    => $tx->payment_url,
            'tagihan_id'     => $tx->tagihan_id,
            'tagihan_lunas'  => $tx->tagihan?->isLunas() ?? false,
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    // Halaman sukses / gagal (redirect dari Pakasir)
    // ──────────────────────────────────────────────────────────────

    public function sukses(Request $request): View|RedirectResponse
    {
        $ref     = $request->query('ref');
        $tagihan = null;

        if ($ref) {
            $tx = PakasirTransaction::where('merchant_ref', $ref)->latest()->first();
            if ($tx) {
                $tagihan = $tx->tagihan()->with('pelanggan')->first();
            }
        }

        if (!$tagihan && $ref) {
            $tagihan = TagihanAir::where('nomor_tagihan', $ref)->with('pelanggan')->first();
        }

        if (!$tagihan) {
            return redirect()->route('pelanggan.tagihan.index')
                ->with('success', 'Pembayaran berhasil! Tagihan akan diperbarui segera.');
        }

        return redirect()->route('pelanggan.tagihan.show', $tagihan)
            ->with('success', '✅ Pembayaran berhasil! Tagihan ' . $tagihan->nomor_tagihan . ' sedang diverifikasi.');
    }

    public function gagal(Request $request): View|RedirectResponse
    {
        $ref     = $request->query('ref');
        $tagihan = null;

        if ($ref) {
            $tagihan = TagihanAir::where('nomor_tagihan', $ref)->first();
        }

        if (!$tagihan) {
            return redirect()->route('pelanggan.tagihan.index')
                ->with('error', 'Pembayaran gagal atau dibatalkan. Silakan coba kembali.');
        }

        return redirect()->route('pelanggan.tagihan.show', $tagihan)
            ->with('error', 'Pembayaran gagal atau dibatalkan. Silakan coba kembali atau hubungi petugas.');
    }

    // ──────────────────────────────────────────────────────────────
    // WEBHOOK — dipanggil oleh Pakasir setelah pembayaran
    // ──────────────────────────────────────────────────────────────

    /**
     * Terima callback dari Pakasir.
     * Route: POST /api/pakasir/callback
     * Middleware: verify.pakasir.webhook (sudah memverifikasi signature)
     */
    public function webhook(Request $request): JsonResponse
    {
        $payload = $request->all();

        Log::info('[Pakasir] Webhook diterima', $payload);

        $merchantRef    = $payload['merchant_ref']    ?? null;
        $pakasirTxId    = $payload['transaction_id']  ?? $payload['id'] ?? null;
        $pakasirStatus  = $payload['status']           ?? 'pending';
        $amount         = $payload['amount']           ?? 0;

        if (!$merchantRef) {
            Log::error('[Pakasir] Webhook: merchant_ref tidak ada dalam payload');
            return response()->json(['message' => 'merchant_ref diperlukan.'], 422);
        }

        // Cari PakasirTransaction berdasarkan merchant_ref
        $tx = PakasirTransaction::where('merchant_ref', $merchantRef)->latest()->first();

        if (!$tx) {
            Log::error('[Pakasir] Webhook: PakasirTransaction tidak ditemukan', [
                'merchant_ref' => $merchantRef,
            ]);
            return response()->json(['message' => 'Transaksi tidak ditemukan.'], 404);
        }

        $mappedStatus = $this->pakasirService->mapStatus($pakasirStatus);

        // Update PakasirTransaction
        $updateData = [
            'status'                 => $mappedStatus,
            'raw_webhook'            => $payload,
        ];

        if ($pakasirTxId && !$tx->pakasir_transaction_id) {
            $updateData['pakasir_transaction_id'] = $pakasirTxId;
        }

        if ($mappedStatus === 'paid') {
            $updateData['paid_at'] = now();
        }

        $tx->update($updateData);
        $tx->refresh();

        // Proses jika dibayar
        if ($mappedStatus === 'paid') {
            $this->prosesTagihanLunas($tx, $payload);
        }

        return response()->json(['success' => true, 'message' => 'Webhook diproses.']);
    }

    // ──────────────────────────────────────────────────────────────
    // Private: proses tagihan lunas setelah webhook paid
    // ──────────────────────────────────────────────────────────────

    private function prosesTagihanLunas(PakasirTransaction $tx, array $payload): void
    {
        $tagihan = $tx->tagihan;

        if (!$tagihan) {
            Log::error('[Pakasir] prosesTagihanLunas: tagihan tidak ditemukan', [
                'pakasir_tx_id' => $tx->id,
                'tagihan_id'    => $tx->tagihan_id,
            ]);
            return;
        }

        // Jika tagihan sudah lunas, jangan proses ulang (idempotent)
        if ($tagihan->isLunas()) {
            Log::info('[Pakasir] prosesTagihanLunas: tagihan sudah lunas, skip', [
                'tagihan_id'   => $tagihan->id,
                'nomor_tagihan'=> $tagihan->nomor_tagihan,
            ]);
            return;
        }

        // Buat record Pembayaran
        $seq = Pembayaran::whereDate('created_at', today())->count() + 1;
        $nomorPembayaran = 'PAY-' . now()->format('Ymd') . '-' . str_pad($seq, 5, '0', STR_PAD_LEFT);

        $pembayaran = Pembayaran::create([
            'tagihan_id'      => $tagihan->id,
            'pelanggan_id'    => $tagihan->pelanggan_id,
            'nomor_pembayaran'=> $nomorPembayaran,
            'jumlah_bayar'    => $tx->amount,
            'tanggal_bayar'   => now()->toDateString(),
            'metode_bayar'    => 'pakasir',
            'status'          => 'konfirmasi',
            'dikonfirmasi_oleh'=> null,
            'catatan'         => 'Otomatis via Pakasir. Ref: ' . $tx->merchant_ref
                                 . ($tx->pakasir_transaction_id ? ' | TX: ' . $tx->pakasir_transaction_id : ''),
        ]);

        // Tautkan pembayaran ke PakasirTransaction
        $tx->update(['pembayaran_id' => $pembayaran->id]);

        // Update status tagihan menjadi lunas
        $tagihan->update(['status' => 'lunas']);

        // Kirim notifikasi ke pelanggan
        if ($tagihan->pelanggan?->user_id) {
            Notifikasi::kirim(
                $tagihan->pelanggan->user_id,
                '✅ Pembayaran Dikonfirmasi',
                "Pembayaran tagihan {$tagihan->nomor_tagihan} ({$tagihan->periodeTeks()}) sebesar Rp " .
                    number_format($tx->amount, 0, ',', '.') . " telah dikonfirmasi via Pakasir.",
                'success',
                route('pelanggan.tagihan.show', $tagihan->id)
            );
        }

        // Catat aktivitas
        AktivitasLog::catat(
            'pembayaran_pakasir_otomatis',
            "Pembayaran {$nomorPembayaran} untuk tagihan {$tagihan->nomor_tagihan} dikonfirmasi via webhook Pakasir.",
            'Pembayaran',
            $pembayaran->id
        );

        Log::info('[Pakasir] prosesTagihanLunas: sukses', [
            'tagihan_id'       => $tagihan->id,
            'nomor_tagihan'    => $tagihan->nomor_tagihan,
            'nomor_pembayaran' => $nomorPembayaran,
            'amount'           => $tx->amount,
        ]);
    }
}