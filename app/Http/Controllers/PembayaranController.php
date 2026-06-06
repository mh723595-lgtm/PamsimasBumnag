<?php

namespace App\Http\Controllers;

use App\Models\TagihanAir;
use App\Models\Pembayaran;
use App\Models\Notifikasi;
use App\Models\AktivitasLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Handler webhook Midtrans (dipanggil dari routes/api.php).
 * Semua logic pembayaran manual ada di Admin\PembayaranController.
 */
class PembayaranController extends Controller
{
    /**
     * Notifikasi otomatis dari server Midtrans.
     * Route: POST /api/midtrans/notifikasi
     */
    public function notifikasi(Request $request)
    {
        try {
            // ── Verifikasi signature Midtrans ────────────────────────────────
            // Signature = SHA512(order_id + status_code + gross_amount + server_key)
            $payload     = $request->all();
            $serverKey   = config('services.midtrans.server_key', '');
            $orderId     = $payload['order_id'] ?? '';
            $statusCode  = $payload['status_code'] ?? '';
            $grossAmount = $payload['gross_amount'] ?? '';
            $signatureKey = $payload['signature_key'] ?? '';

            if (empty($serverKey)) {
                Log::error('[Midtrans] MIDTRANS_SERVER_KEY tidak dikonfigurasi. Webhook ditolak.');
                return response()->json(['success' => false, 'message' => 'Server misconfiguration'], 500);
            }

            $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

            if (!hash_equals($expectedSignature, $signatureKey)) {
                Log::warning('[Midtrans] Signature tidak valid. Kemungkinan request palsu.', [
                    'order_id'   => $orderId,
                    'ip'         => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
                return response()->json(['success' => false, 'message' => 'Invalid signature'], 403);
            }
            // ── Akhir verifikasi signature ───────────────────────────────────

            $notif       = new \Midtrans\Notification();
            $status      = $notif->transaction_status;
            $fraudStatus = $notif->fraud_status ?? null;

            Log::info("[Midtrans] Notifikasi diterima", [
                'order_id' => $orderId,
                'status'   => $status,
            ]);

            // order_id format: PAM-{tagihanId}-{timestamp}
            $parts     = explode('-', $orderId);
            $tagihanId = $parts[1] ?? null;

            if (!$tagihanId) {
                Log::warning("[Midtrans] Format order_id tidak dikenal: {$orderId}");
                return response()->json(['success' => false, 'message' => 'Order ID tidak valid'], 400);
            }

            $tagihan = TagihanAir::with('pelanggan')->find($tagihanId);

            if (!$tagihan) {
                Log::warning("[Midtrans] Tagihan ID {$tagihanId} tidak ditemukan");
                return response()->json(['success' => false, 'message' => 'Tagihan tidak ditemukan'], 404);
            }

            $berhasil = ($status === 'capture' && $fraudStatus === 'accept')
                     || $status === 'settlement';

            if ($berhasil && !$tagihan->isLunas()) {
                // Cek sudah ada record pembayaran atau belum
                $sudahAda = Pembayaran::where('tagihan_id', $tagihan->id)->exists();

                if (!$sudahAda) {
                    $seq = Pembayaran::whereDate('created_at', today())->count() + 1;
                    Pembayaran::create([
                        'tagihan_id'       => $tagihan->id,
                        'pelanggan_id'     => $tagihan->pelanggan_id,
                        'nomor_pembayaran' => 'PAY-' . now()->format('Ymd') . '-' . str_pad($seq, 5, '0', STR_PAD_LEFT),
                        'jumlah_bayar'     => $tagihan->total_bayar ?: $tagihan->total_tagihan,
                        'tanggal_bayar'    => now()->toDateString(),
                        'metode_bayar'     => 'transfer',
                        'status'           => 'konfirmasi',
                        'catatan'          => 'Dikonfirmasi otomatis via Midtrans. Order ID: ' . $orderId,
                    ]);
                }

                $tagihan->update(['status' => 'lunas']);

                if ($tagihan->pelanggan?->user_id) {
                    Notifikasi::kirim(
                        $tagihan->pelanggan->user_id,
                        '✅ Pembayaran Berhasil',
                        "Pembayaran tagihan {$tagihan->nomor_tagihan} telah dikonfirmasi melalui Midtrans.",
                        'success'
                    );
                }

                AktivitasLog::catat(
                    'midtrans_notifikasi',
                    "Tagihan {$tagihan->nomor_tagihan} lunas via Midtrans. Order ID: {$orderId}",
                    'TagihanAir',
                    $tagihan->id
                );

                Log::info("[Midtrans] Tagihan {$tagihan->nomor_tagihan} berhasil dilunasi.");
            } elseif (in_array($status, ['cancel', 'deny', 'expire', 'failure'])) {
                Log::info("[Midtrans] Pembayaran {$orderId} gagal/dibatalkan. Status: {$status}");
                // Tidak ubah status tagihan — biarkan tetap belum_bayar / terlambat
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error("[Midtrans] Error webhook: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['success' => false, 'message' => 'Internal error'], 500);
        }
    }
}