<?php

namespace App\Http\Controllers;

use App\Models\TagihanAir;
use App\Models\Pembayaran;
use App\Models\Notifikasi;
use App\Models\AktivitasLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PembayaranController extends Controller
{
    public function notifikasi(Request $request)
    {
        try {
            $notif       = new \Midtrans\Notification();
            $status      = $notif->transaction_status;
            $orderId     = $notif->order_id;
            $fraudStatus = $notif->fraud_status ?? null;

            Log::info('[Midtrans] Notifikasi diterima', [
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
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('[Midtrans] Error webhook: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['success' => false, 'message' => 'Internal error'], 500);
        }
    }
}