<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Services\DendaService;

/*
|--------------------------------------------------------------------------
| Console Routes (Artisan Commands)
|--------------------------------------------------------------------------
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
 * Proses denda otomatis manual via artisan
 * Usage: php artisan pamsimas:proses-denda
 */
Artisan::command('pamsimas:proses-denda', function () {
    $this->info('Memproses denda keterlambatan...');

    $dendaService = app(DendaService::class);
    $hasil = $dendaService->prosesSemuaDenda();

    $this->info("✅ Selesai: {$hasil['diproses']} tagihan diproses");
    $this->info("   Total denda: Rp " . number_format($hasil['total_denda'], 0, ',', '.'));
})->purpose('Proses denda keterlambatan tagihan air secara manual');

/*
 * Update status tagihan terlambat
 * Usage: php artisan pamsimas:update-status
 */
Artisan::command('pamsimas:update-status', function () {
    $this->info('Mengupdate status tagihan...');

    $updated = \App\Models\TagihanAir::where('status', 'belum_bayar')
        ->where('tanggal_jatuh_tempo', '<', now()->toDateString())
        ->update(['status' => 'terlambat']);

    $this->info("✅ {$updated} tagihan diubah ke status terlambat");
})->purpose('Update status tagihan belum_bayar yang sudah melewati jatuh tempo');
