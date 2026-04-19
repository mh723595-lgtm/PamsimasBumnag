<?php

namespace App\Services;

use App\Models\Pelanggan;
use App\Models\MeteranAir;
use App\Models\TagihanAir;
use Carbon\Carbon;
use Illuminate\Support\Str;

class TagihanService
{
    /**
     * Hitung total tagihan berdasarkan pemakaian (m³)
     * Tarif global untuk semua pelanggan
     */
    public function hitungTagihan(float $pemakaian): array
    {
        $total = 0;

        if ($pemakaian <= 10) {
            $total = 20000;
        } else {
            $total = 20000;

            if ($pemakaian > 10) {
                $blok2 = min($pemakaian, 20) - 10;
                $total += $blok2 * 1500;
            }

            if ($pemakaian > 20) {
                $blok3 = $pemakaian - 20;
                $total += $blok3 * 2000;
            }
        }

        // Biaya administrasi
        $total += 2500;

        return [
            'pemakaian'   => $pemakaian,
            'biaya_pokok' => $total - 2500,
            'biaya_admin' => 2500,
            'total'       => $total,
            'rincian'     => $this->rincianTarif($pemakaian),
        ];
    }

    /**
     * Rincian perhitungan tarif per blok
     */
    public function rincianTarif(float $pemakaian): array
    {
        $rincian = [];

        // Blok 1: 0-10 m³
        $blok1 = min($pemakaian, 10);
        $rincian[] = [
            'blok'   => 'Blok 1 (0-10 m³)',
            'volume' => $blok1,
            'tarif'  => 0, // flat
            'biaya'  => 20000,
            'note'   => 'Tarif tetap Rp 20.000',
        ];

        // Blok 2: 11-20 m³
        if ($pemakaian > 10) {
            $blok2 = min($pemakaian, 20) - 10;
            $rincian[] = [
                'blok'   => 'Blok 2 (11-20 m³)',
                'volume' => $blok2,
                'tarif'  => 1500,
                'biaya'  => $blok2 * 1500,
                'note'   => 'Rp 1.500/m³',
            ];
        }

        // Blok 3: >20 m³
        if ($pemakaian > 20) {
            $blok3 = $pemakaian - 20;
            $rincian[] = [
                'blok'   => 'Blok 3 (>20 m³)',
                'volume' => $blok3,
                'tarif'  => 2000,
                'biaya'  => $blok3 * 2000,
                'note'   => 'Rp 2.000/m³',
            ];
        }

        // Biaya admin
        $rincian[] = [
            'blok'   => 'Biaya Administrasi',
            'volume' => '-',
            'tarif'  => '-',
            'biaya'  => 2500,
            'note'   => 'Flat',
        ];

        return $rincian;
    }

    /**
     * Generate tagihan dari data meteran
     */
    public function generateDariMeteran(MeteranAir $meteran): TagihanAir
    {
        // Cek sudah ada tagihan untuk periode ini
        $existing = TagihanAir::where('pelanggan_id', $meteran->pelanggan_id)
            ->where('bulan', $meteran->bulan)
            ->where('tahun', $meteran->tahun)
            ->first();

        if ($existing) {
            return $existing;
        }

        $hasil = $this->hitungTagihan((float) ($meteran->pemakaian ?? 0));

        $tagihan = TagihanAir::create([
            'pelanggan_id'        => $meteran->pelanggan_id,
            'meteran_id'          => $meteran->id,
            'nomor_tagihan'       => $this->generateNomorTagihan($meteran->bulan, $meteran->tahun),
            'bulan'               => $meteran->bulan,
            'tahun'               => $meteran->tahun,
            'pemakaian'           => $meteran->pemakaian,
            'total_tagihan'       => $hasil['total'],
            'tanggal_tagihan'     => now(),
            'tanggal_jatuh_tempo' => Carbon::create($meteran->tahun, $meteran->bulan, 1)->endOfMonth(),
            'status'              => 'belum_bayar',
        ]);

        return $tagihan;
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
     * Update status tagihan yang melewati jatuh tempo
     */
    public function updateStatusTerlambat(): int
    {
        return TagihanAir::where('status', 'belum_bayar')
            ->where('tanggal_jatuh_tempo', '<', now()->toDateString())
            ->update(['status' => 'terlambat']);
    }

    /**
     * Format rupiah
     */
    public static function formatRupiah(float $nominal): string
    {
        return 'Rp ' . number_format($nominal, 0, ',', '.');
    }

    /**
     * Nama bulan dalam Bahasa Indonesia
     */
    public static function namaBulan(int $bulan): string
    {
        $bulanArr = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
        return $bulanArr[$bulan] ?? '-';
    }
}
