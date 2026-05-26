<?php

namespace App\Services;

use App\Models\MeteranAir;
use App\Models\SettingAplikasi;
use App\Models\TagihanAir;
use Carbon\Carbon;

class TagihanService
{
    /**
     * Ambil semua konfigurasi tarif
     */
    protected function getTarifConfig(): array
    {
        return [
            'tarif_blok1' => (float) SettingAplikasi::get('tarif_blok1', 20000),
            'tarif_blok2' => (float) SettingAplikasi::get('tarif_blok2', 1500),
            'tarif_blok3' => (float) SettingAplikasi::get('tarif_blok3', 2000),
            'biaya_admin' => (float) SettingAplikasi::get('biaya_admin', 2500),
        ];
    }

    /**
     * Hitung total tagihan berdasarkan pemakaian air
     */
    public function hitungTagihan(float $pemakaian): array
    {
        $pemakaian = max(0, $pemakaian);

        $config = $this->getTarifConfig();

        $tarif1 = $config['tarif_blok1'];
        $tarif2 = $config['tarif_blok2'];
        $tarif3 = $config['tarif_blok3'];
        $biayaAdmin = $config['biaya_admin'];

        $biayaPokok = 0;

        /**
         * Blok 1 (0-10 m³)
         * Tarif flat
         */
        $biayaPokok += $tarif1;

        /**
         * Blok 2 (11-20 m³)
         */
        if ($pemakaian > 10) {
            $blok2 = min($pemakaian, 20) - 10;
            $biayaPokok += $blok2 * $tarif2;
        }

        /**
         * Blok 3 (>20 m³)
         */
        if ($pemakaian > 20) {
            $blok3 = $pemakaian - 20;
            $biayaPokok += $blok3 * $tarif3;
        }

        $total = $biayaPokok + $biayaAdmin;

        return [
            'pemakaian'   => $pemakaian,
            'biaya_pokok' => $biayaPokok,
            'biaya_admin' => $biayaAdmin,
            'total'       => $total,
            'rincian'     => $this->rincianTarif(
                $pemakaian,
                $tarif1,
                $tarif2,
                $tarif3,
                $biayaAdmin
            ),
        ];
    }

    /**
     * Rincian tarif berdasarkan blok pemakaian
     */
    public function rincianTarif(
        float $pemakaian,
        float $tarif1,
        float $tarif2,
        float $tarif3,
        float $biayaAdmin
    ): array {
        $rincian = [];

        /**
         * Blok 1
         */
        $blok1 = min($pemakaian, 10);

        $rincian[] = [
            'blok'   => 'Blok 1 (0-10 m³)',
            'volume' => $blok1,
            'tarif'  => $tarif1,
            'biaya'  => $tarif1,
            'note'   => 'Tarif tetap',
        ];

        /**
         * Blok 2
         */
        if ($pemakaian > 10) {
            $blok2 = min($pemakaian, 20) - 10;

            $rincian[] = [
                'blok'   => 'Blok 2 (11-20 m³)',
                'volume' => $blok2,
                'tarif'  => $tarif2,
                'biaya'  => $blok2 * $tarif2,
                'note'   => self::formatRupiah($tarif2) . '/m³',
            ];
        }

        /**
         * Blok 3
         */
        if ($pemakaian > 20) {
            $blok3 = $pemakaian - 20;

            $rincian[] = [
                'blok'   => 'Blok 3 (>20 m³)',
                'volume' => $blok3,
                'tarif'  => $tarif3,
                'biaya'  => $blok3 * $tarif3,
                'note'   => self::formatRupiah($tarif3) . '/m³',
            ];
        }

        /**
         * Biaya admin
         */
        $rincian[] = [
            'blok'   => 'Biaya Administrasi',
            'volume' => '-',
            'tarif'  => $biayaAdmin,
            'biaya'  => $biayaAdmin,
            'note'   => 'Flat',
        ];

        return $rincian;
    }

    /**
     * Generate tagihan dari meteran
     */
    public function generateDariMeteran(MeteranAir $meteran): TagihanAir
    {
        $existing = TagihanAir::where('pelanggan_id', $meteran->pelanggan_id)
            ->where('bulan', $meteran->bulan)
            ->where('tahun', $meteran->tahun)
            ->first();

        if ($existing) {
            return $existing;
        }

        $hasil = $this->hitungTagihan($meteran->pemakaian);

        return TagihanAir::create([
            'pelanggan_id'        => $meteran->pelanggan_id,
            'meteran_id'          => $meteran->id,
            'nomor_tagihan'       => $this->generateNomorTagihan(
                $meteran->bulan,
                $meteran->tahun
            ),
            'bulan'               => $meteran->bulan,
            'tahun'               => $meteran->tahun,
            'pemakaian'           => $meteran->pemakaian,
            'total_tagihan'       => $hasil['total'],
            'tanggal_tagihan'     => now(),
            'tanggal_jatuh_tempo' => Carbon::create(
                $meteran->tahun,
                $meteran->bulan,
                1
            )->endOfMonth(),
            'status'              => 'belum_bayar',
        ]);
    }

    /**
     * Generate nomor tagihan unik
     */
    public function generateNomorTagihan(int $bulan, int $tahun): string
    {
        $prefix = 'TGH';

        $period = str_pad($bulan, 2, '0', STR_PAD_LEFT) . $tahun;

        $seq = TagihanAir::whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->count() + 1;

        $seq = str_pad($seq, 4, '0', STR_PAD_LEFT);

        return "{$prefix}-{$period}-{$seq}";
    }

    /**
     * Update status tagihan terlambat
     */
    public function updateStatusTerlambat(): int
    {
        return TagihanAir::where('status', 'belum_bayar')
            ->whereDate('tanggal_jatuh_tempo', '<', now())
            ->update([
                'status' => 'terlambat',
            ]);
    }

    /**
     * Format rupiah
     */
    public static function formatRupiah(float $nominal): string
    {
        return 'Rp ' . number_format($nominal, 0, ',', '.');
    }

    /**
     * Nama bulan Indonesia
     */
    public static function namaBulan(int $bulan): string
    {
        $bulanArr = [
            1  => 'Januari',
            2  => 'Februari',
            3  => 'Maret',
            4  => 'April',
            5  => 'Mei',
            6  => 'Juni',
            7  => 'Juli',
            8  => 'Agustus',
            9  => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        return $bulanArr[$bulan] ?? '-';
    }
}