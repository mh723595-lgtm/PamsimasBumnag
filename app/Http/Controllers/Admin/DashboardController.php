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
        $bulanIni  = now()->month;
        $tahunIni  = now()->year;

        // Stat cards
        $totalPelanggan   = Pelanggan::where('status', 'aktif')->count();
        $tagihanBulanIni  = TagihanAir::where('bulan', $bulanIni)->where('tahun', $tahunIni)->count();
        $tagihanLunas     = TagihanAir::where('bulan', $bulanIni)->where('tahun', $tahunIni)->where('status', 'lunas')->count();
        $tagihanBelumBayar= TagihanAir::where('bulan', $bulanIni)->where('tahun', $tahunIni)->whereIn('status', ['belum_bayar','terlambat'])->count();
        $pendapatanBulanIni = Pembayaran::where('status', 'konfirmasi')
            ->whereMonth('tanggal_bayar', $bulanIni)
            ->whereYear('tanggal_bayar', $tahunIni)
            ->sum('jumlah_bayar');
        $pengaduanBaru = Pengaduan::where('status', 'baru')->count();
        $totalPemakaian = MeteranAir::where('bulan', $bulanIni)->where('tahun', $tahunIni)->sum('pemakaian');

        // Chart data: 6 bulan terakhir pendapatan
        $chartPendapatan = [];
        $chartLabel = [];
        for ($i = 5; $i >= 0; $i--) {
            $dt = Carbon::now()->subMonths($i);
            $chartLabel[] = $dt->translatedFormat('M Y');
            $chartPendapatan[] = Pembayaran::where('status', 'konfirmasi')
                ->whereMonth('tanggal_bayar', $dt->month)
                ->whereYear('tanggal_bayar', $dt->year)
                ->sum('jumlah_bayar');
        }

        // Chart pemakaian 6 bulan
        $chartPemakaian = [];
        for ($i = 5; $i >= 0; $i--) {
            $dt = Carbon::now()->subMonths($i);
            $chartPemakaian[] = MeteranAir::where('bulan', $dt->month)->where('tahun', $dt->year)->sum('pemakaian');
        }

        // Tagihan terbaru
        $tagihanTerbaru = TagihanAir::with('pelanggan')
            ->latest()
            ->take(6)
            ->get();

        // Pengaduan terbaru
        $pengaduanTerbaru = Pengaduan::with('pelanggan')
            ->latest()
            ->take(5)
            ->get();

        // Status tagihan donut
        $tagihanLunasTotal     = TagihanAir::where('status', 'lunas')->count();
        $tagihanBelumBayarTotal = TagihanAir::where('status', 'belum_bayar')->count();
        $tagihanTerlambatTotal  = TagihanAir::where('status', 'terlambat')->count();

        return view('admin.dashboard', compact(
            'totalPelanggan', 'tagihanBulanIni', 'tagihanLunas',
            'tagihanBelumBayar', 'pendapatanBulanIni', 'pengaduanBaru',
            'totalPemakaian', 'chartLabel', 'chartPendapatan', 'chartPemakaian',
            'tagihanTerbaru', 'pengaduanTerbaru',
            'tagihanLunasTotal', 'tagihanBelumBayarTotal', 'tagihanTerlambatTotal'
        ));
    }
}
