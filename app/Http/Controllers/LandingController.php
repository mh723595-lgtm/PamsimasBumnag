<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\TagihanAir;
use App\Models\MeteranAir;
use App\Models\SettingAplikasi;
use Carbon\Carbon;

class LandingController extends Controller
{
    public function index()
    {
        $bulanIni = now()->month;
        $tahunIni = now()->year;

        // ── Statistik utama ──────────────────────────────────────────────────
        $totalPelanggan = Pelanggan::where('status', 'aktif')->count();

        // Pemakaian bulan ini (untuk hero card)
        $pemakaianBulanIni = MeteranAir::where('bulan', $bulanIni)
            ->where('tahun', $tahunIni)
            ->sum('pemakaian');

        // Pemakaian kumulatif semua waktu (pengganti pendapatan di stat card)
        $pemakaianKumulatif = MeteranAir::sum('pemakaian');

        // Persentase tagihan lunas & total tagihan bulan ini
        $tagihanStats = TagihanAir::where('bulan', $bulanIni)
            ->where('tahun', $tahunIni)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'lunas' THEN 1 ELSE 0 END) as lunas
            ")
            ->first();

        $totalTagihanBulanIni = (int) ($tagihanStats->total ?? 0);
        $tagihanLunas         = (int) ($tagihanStats->lunas ?? 0);
        $persenLunas          = $totalTagihanBulanIni > 0
            ? round($tagihanLunas / $totalTagihanBulanIni * 100, 1)
            : 0;

        // ── Hero card mini stats ─────────────────────────────────────────────
        $heroTagihanLunas = $tagihanLunas;
        $heroPemakaian    = $pemakaianBulanIni;

        // ── Chart 6 bulan ────────────────────────────────────────────────────
        $start = Carbon::now()->subMonths(5)->startOfMonth();

        $pemakaianRaw = MeteranAir::where('tanggal_baca', '>=', $start->toDateString())
            ->selectRaw('bulan as bln, tahun as thn, SUM(pemakaian) as total')
            ->groupBy('tahun', 'bulan')
            ->get()
            ->keyBy(fn($r) => "{$r->thn}-{$r->bln}");

        $tagihanRaw = TagihanAir::where('created_at', '>=', $start->toDateString())
            ->selectRaw('MONTH(created_at) as bln, YEAR(created_at) as thn, COUNT(*) as total')
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->get()
            ->keyBy(fn($r) => "{$r->thn}-{$r->bln}");

        $pendapatanRaw = TagihanAir::where('created_at', '>=', $start->toDateString())
            ->where('status', 'lunas')
            ->selectRaw('MONTH(created_at) as bln, YEAR(created_at) as thn, SUM(total_bayar) as total')
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->get()
            ->keyBy(fn($r) => "{$r->thn}-{$r->bln}");

        $chartLabels     = [];
        $chartPemakaian  = [];
        $chartTagihan    = [];
        $chartPendapatan = [];

        for ($i = 5; $i >= 0; $i--) {
            $dt  = Carbon::now()->subMonths($i);
            $key = "{$dt->year}-{$dt->month}";
            $chartLabels[]     = $dt->translatedFormat('M Y');
            $chartPemakaian[]  = (int) ($pemakaianRaw[$key]->total ?? 0);
            $chartTagihan[]    = (int) ($tagihanRaw[$key]->total ?? 0);
            $chartPendapatan[] = (int) ($pendapatanRaw[$key]->total ?? 0);
        }

        // ── Setting ──────────────────────────────────────────────────────────
        $namaSistem = SettingAplikasi::get('nama_sistem', 'PAMSIMAS');
        $namaDesa   = SettingAplikasi::get('nama_desa', 'Desa');

        return view('landing', compact(
            'totalPelanggan',
            'pemakaianBulanIni',
            'pemakaianKumulatif',
            'persenLunas',
            'heroTagihanLunas',
            'heroPemakaian',
            'totalTagihanBulanIni',
            'chartLabels',
            'chartPemakaian',
            'chartTagihan',
            'namaSistem',
            'namaDesa',
            'chartPendapatan'
        ));
    }
}