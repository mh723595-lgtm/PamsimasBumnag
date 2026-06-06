<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\TagihanAir;
use App\Models\Pembayaran;
use App\Models\Pengaduan;
use App\Models\MeteranAir;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $bulanIni = now()->month;
        $tahunIni = now()->year;

        // ── Stat cards: satu query per model, bukan banyak ──────────────────
        $tagihanBulanIniStats = TagihanAir::where('bulan', $bulanIni)
            ->where('tahun', $tahunIni)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'lunas' THEN 1 ELSE 0 END) as lunas,
                SUM(CASE WHEN status IN ('belum_bayar','terlambat') THEN 1 ELSE 0 END) as belum_bayar
            ")
            ->first();

        $totalPelanggan      = Pelanggan::where('status', 'aktif')->count();
        $tagihanBulanIni     = (int) ($tagihanBulanIniStats->total ?? 0);
        $tagihanLunas        = (int) ($tagihanBulanIniStats->lunas ?? 0);
        $tagihanBelumBayar   = (int) ($tagihanBulanIniStats->belum_bayar ?? 0);

        $pendapatanBulanIni  = Pembayaran::where('status', 'konfirmasi')
            ->whereMonth('tanggal_bayar', $bulanIni)
            ->whereYear('tanggal_bayar', $tahunIni)
            ->sum('jumlah_bayar');

        $pengaduanBaru  = Pengaduan::where('status', 'baru')->count();
        $totalPemakaian = MeteranAir::where('bulan', $bulanIni)->where('tahun', $tahunIni)->sum('pemakaian');

        // ── Chart pendapatan 6 bulan: SATU query, group by bulan+tahun ───────
        $start = Carbon::now()->subMonths(5)->startOfMonth();

        $pendapatanRaw = Pembayaran::where('status', 'konfirmasi')
            ->where('tanggal_bayar', '>=', $start->toDateString())
            ->selectRaw('MONTH(tanggal_bayar) as bln, YEAR(tanggal_bayar) as thn, SUM(jumlah_bayar) as total')
            ->groupByRaw('YEAR(tanggal_bayar), MONTH(tanggal_bayar)')
            ->get()
            ->keyBy(fn($r) => "{$r->thn}-{$r->bln}");

        $pemakaianRaw = MeteranAir::where('tanggal_baca', '>=', $start->toDateString())
            ->selectRaw('bulan as bln, tahun as thn, SUM(pemakaian) as total')
            ->groupBy('tahun', 'bulan')
            ->get()
            ->keyBy(fn($r) => "{$r->thn}-{$r->bln}");

        $chartLabel      = [];
        $chartPendapatan = [];
        $chartPemakaian  = [];

        for ($i = 5; $i >= 0; $i--) {
            $dt  = Carbon::now()->subMonths($i);
            $key = "{$dt->year}-{$dt->month}";
            $chartLabel[]      = $dt->translatedFormat('M Y');
            $chartPendapatan[] = (float) ($pendapatanRaw[$key]->total ?? 0);
            $chartPemakaian[]  = (float) ($pemakaianRaw[$key]->total ?? 0);
        }

        // ── Donut chart status tagihan: satu query ───────────────────────────
        $statusCounts = TagihanAir::selectRaw("
                SUM(CASE WHEN status = 'lunas' THEN 1 ELSE 0 END) as lunas,
                SUM(CASE WHEN status = 'belum_bayar' THEN 1 ELSE 0 END) as belum_bayar,
                SUM(CASE WHEN status = 'terlambat' THEN 1 ELSE 0 END) as terlambat
            ")
            ->first();

        $tagihanLunasTotal      = (int) ($statusCounts->lunas ?? 0);
        $tagihanBelumBayarTotal = (int) ($statusCounts->belum_bayar ?? 0);
        $tagihanTerlambatTotal  = (int) ($statusCounts->terlambat ?? 0);

        // ── Recent records ───────────────────────────────────────────────────
        $tagihanTerbaru   = TagihanAir::with('pelanggan')->latest()->take(6)->get();
        $pengaduanTerbaru = Pengaduan::with('pelanggan')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalPelanggan', 'tagihanBulanIni', 'tagihanLunas',
            'tagihanBelumBayar', 'pendapatanBulanIni', 'pengaduanBaru',
            'totalPemakaian', 'chartLabel', 'chartPendapatan', 'chartPemakaian',
            'tagihanTerbaru', 'pengaduanTerbaru',
            'tagihanLunasTotal', 'tagihanBelumBayarTotal', 'tagihanTerlambatTotal'
        ));
    }
}