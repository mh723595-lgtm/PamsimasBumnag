<?php

namespace App\Services;

use App\Models\TagihanAir;
use App\Models\SettingAplikasi;
use App\Models\Notifikasi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DendaService
{
    /**
     * Ambil konfigurasi denda dari setting (fallback ke default)
     */
    public function getKonfigurasi(): array
    {
        return [
            'persen_denda_per_bulan' => (float) SettingAplikasi::get('denda_persen', 2),   // 2% per bulan
            'denda_minimum'          => (float) SettingAplikasi::get('denda_minimum', 2000), // Rp 2.000 minimum
            'denda_maksimum_persen'  => (float) SettingAplikasi::get('denda_maksimum_persen', 50), // max 50% dari tagihan
            'grace_period_hari'      => (int)   SettingAplikasi::get('denda_grace_period', 0), // toleransi hari
            'aktif'                  => (bool)  SettingAplikasi::get('denda_aktif', true),
        ];
    }

    /**
     * Hitung denda untuk satu tagihan berdasarkan hari keterlambatan
     *
     * @param  TagihanAir  $tagihan
     * @param  Carbon|null $tanggalHitung  Tanggal referensi (default: hari ini)
     * @return array ['denda' => float, 'hari_terlambat' => int, 'bulan_terlambat' => int, 'detail' => string]
     */
    public function hitungDenda(TagihanAir $tagihan, ?Carbon $tanggalHitung = null): array
    {
        $tanggalHitung = $tanggalHitung ?? Carbon::today();
        $config = $this->getKonfigurasi();

        // Tidak aktif atau sudah lunas
        if (!$config['aktif'] || $tagihan->isLunas()) {
            return ['denda' => 0, 'hari_terlambat' => 0, 'bulan_terlambat' => 0, 'detail' => 'Tidak ada denda'];
        }

        $jatuhTempo = Carbon::parse($tagihan->tanggal_jatuh_tempo);

        // Belum jatuh tempo (gunakan copy() agar $jatuhTempo tidak termutasi)
        if ($tanggalHitung->lte($jatuhTempo->copy()->addDays($config['grace_period_hari']))) {
            return ['denda' => 0, 'hari_terlambat' => 0, 'bulan_terlambat' => 0, 'detail' => 'Belum jatuh tempo'];
        }

        // Hitung keterlambatan dari tanggal jatuh tempo asli (bukan yang sudah di-addDays)
        $hariTerlambat   = $jatuhTempo->diffInDays($tanggalHitung);
        $bulanTerlambat  = max(1, (int) ceil($hariTerlambat / 30)); // minimal 1 bulan jika sudah lewat

        // Hitung denda: persen × tagihan pokok × bulan
        $dendaAmount = ($config['persen_denda_per_bulan'] / 100) * $tagihan->total_tagihan * $bulanTerlambat;

        // Pastikan minimum
        $dendaAmount = max($dendaAmount, $config['denda_minimum']);

        // Pastikan tidak melebihi maksimum
        $dendaMaks = ($config['denda_maksimum_persen'] / 100) * $tagihan->total_tagihan;
        $dendaAmount = min($dendaAmount, $dendaMaks);

        $detail = sprintf(
            '%d%% × Rp %s × %d bln = Rp %s',
            $config['persen_denda_per_bulan'],
            number_format($tagihan->total_tagihan, 0, ',', '.'),
            $bulanTerlambat,
            number_format($dendaAmount, 0, ',', '.')
        );

        return [
            'denda'          => round($dendaAmount),
            'hari_terlambat' => $hariTerlambat,
            'bulan_terlambat'=> $bulanTerlambat,
            'detail'         => $detail,
        ];
    }

    /**
     * Terapkan denda ke semua tagihan terlambat yang belum terkena denda / perlu diupdate
     * Dipanggil oleh scheduler harian
     *
     * @return array ['diproses' => int, 'total_denda' => float]
     */
    public function prosesSemuaDenda(): array
    {
        $config = $this->getKonfigurasi();
        if (!$config['aktif']) {
            Log::info('[DendaService] Denda nonaktif, tidak diproses.');
            return ['diproses' => 0, 'total_denda' => 0];
        }

        $diproses   = 0;
        $totalDenda = 0;


        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\TagihanAir> $tagihan */
        // Ambil semua tagihan yang sudah melewati jatuh tempo dan belum lunas
        $tagihan = TagihanAir::with('pelanggan.user')
            ->whereIn('status', ['belum_bayar', 'terlambat'])
            ->where('tanggal_jatuh_tempo', '<', Carbon::today()->subDays($config['grace_period_hari']))
            ->get();

        foreach ($tagihan as $t) {
            try {
                $hasil = $this->hitungDenda($t);

                if ($hasil['denda'] <= 0) continue;

                $totalBayar = (float) $t->total_tagihan + (float) $hasil['denda'];

                // Update tagihan
                $needsNotif = $t->status !== 'terlambat' || abs($t->denda - $hasil['denda']) > 1;

                $t->update([
                    'status'        => 'terlambat',
                    'denda'         => $hasil['denda'],
                    'total_bayar'   => $totalBayar,
                    'tanggal_denda' => $t->tanggal_denda ?? Carbon::today(),
                ]);

                // Kirim notifikasi jika denda baru atau berubah signifikan
                if ($needsNotif && $t->pelanggan?->user_id) {
                    Notifikasi::kirim(
                        $t->pelanggan->user_id,
                        '⚠️ Denda Keterlambatan Dikenakan',
                        "Tagihan {$t->nomor_tagihan} telah melewati jatuh tempo {$hasil['hari_terlambat']} hari. " .
                        "Denda: Rp " . number_format($hasil['denda'], 0, ',', '.') . ". " .
                        "Total yang harus dibayar: Rp " . number_format($totalBayar, 0, ',', '.'),
                        'warning'
                    );
                }

                $diproses++;
                $totalDenda += $hasil['denda'];

            } catch (\Exception $e) {
                Log::error("[DendaService] Error tagihan {$t->id}: " . $e->getMessage());
            }
        }

        Log::info("[DendaService] Selesai: {$diproses} tagihan, total denda Rp " . number_format($totalDenda, 0, ',', '.'));

        return ['diproses' => $diproses, 'total_denda' => $totalDenda];
    }

    /**
     * Format singkat denda untuk display
     */
    public static function formatDenda(float $denda): string
    {
        if ($denda <= 0) return '-';
        return 'Rp ' . number_format($denda, 0, ',', '.');
    }
}