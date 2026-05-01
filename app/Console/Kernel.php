<?php

namespace App\Console;

use App\Models\TagihanAir;
use App\Models\Notifikasi;
use App\Services\DendaService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Daftarkan jadwal task otomatis.
     * Setup cron di server: * * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1
     */
    protected function schedule(Schedule $schedule): void
    {
        // ── 1. DENDA OTOMATIS ─────────────────────────────────────
        // Jalankan setiap hari jam 00:30 — kenakan denda ke semua tagihan terlambat
        $schedule->call(function () {
            $dendaService = app(DendaService::class);
            $hasil = $dendaService->prosesSemuaDenda();
            Log::info("[PAMSIMAS Scheduler] Denda: {$hasil['diproses']} tagihan diproses, total Rp " . number_format($hasil['total_denda'], 0, ',', '.'));
        })->dailyAt('00:30')
          ->name('proses-denda-otomatis')
          ->withoutOverlapping()
          ->description('Proses denda keterlambatan tagihan air');

        // ── 2. UPDATE STATUS TERLAMBAT ────────────────────────────
        // Jalankan setiap hari jam 00:05
        $schedule->call(function () {
            $updated = TagihanAir::where('status', 'belum_bayar')
                ->where('tanggal_jatuh_tempo', '<', now()->toDateString())
                ->update(['status' => 'terlambat']);

            if ($updated > 0) {
                Log::info("[PAMSIMAS] {$updated} tagihan diubah ke status terlambat.");
            }
        })->dailyAt('00:05')
          ->name('update-tagihan-terlambat')
          ->withoutOverlapping()
          ->description('Update status tagihan belum_bayar → terlambat');

        // ── 3. NOTIFIKASI H-3 JATUH TEMPO ────────────────────────
        $schedule->call(function () {
            $hariMinus = (int) \App\Models\SettingAplikasi::get('notif_h_minus', 3);
            $tglTarget  = now()->addDays($hariMinus)->toDateString();

            $tagihan = TagihanAir::with('pelanggan.user')
                ->where('status', 'belum_bayar')
                ->where('tanggal_jatuh_tempo', $tglTarget)
                ->get();

            foreach ($tagihan as $t) {
                if ($t->pelanggan?->user_id) {
                    Notifikasi::kirim(
                        $t->pelanggan->user_id,
                        '🔔 Tagihan Segera Jatuh Tempo',
                        "Tagihan {$t->nomor_tagihan} sebesar Rp " .
                            number_format($t->total_tagihan, 0, ',', '.') .
                            " akan jatuh tempo pada " .
                            \Carbon\Carbon::parse($t->tanggal_jatuh_tempo)->format('d/m/Y') .
                            ". Harap segera lakukan pembayaran untuk menghindari denda.",
                        'warning'
                    );
                }
            }

            Log::info("[PAMSIMAS] Notifikasi H-{$hariMinus} terkirim ke {$tagihan->count()} pelanggan.");
        })->dailyAt('08:00')
          ->name('notif-jatuh-tempo')
          ->withoutOverlapping()
          ->description('Kirim notifikasi pengingat jatuh tempo');

        // ── 4. BERSIHKAN DATA LAMA ────────────────────────────────
        $schedule->call(function () {
            $n = Notifikasi::where('sudah_dibaca', true)
                ->where('created_at', '<', now()->subDays(30))->delete();
            $l = \App\Models\AktivitasLog::where('created_at', '<', now()->subDays(90))->delete();
            Log::info("[PAMSIMAS] Cleanup: {$n} notifikasi & {$l} log lama dihapus.");
        })->weekly()->sundays()->at('03:00')
          ->name('cleanup-data-lama')
          ->description('Bersihkan notifikasi & log lama');
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
