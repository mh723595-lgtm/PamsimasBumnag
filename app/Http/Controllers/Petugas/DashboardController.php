<?php
// app/Http/Controllers/Petugas/DashboardController.php
namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\MeteranAir;
use App\Models\Pelanggan;
use App\Models\Pengaduan;
use App\Models\TagihanAir;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $petugas   = Auth::user()->petugas;
        $bulanIni  = now()->month;
        $tahunIni  = now()->year;

        // Input meteran bulan ini oleh petugas ini
        $meteranBulanIni = MeteranAir::where('petugas_id', $petugas?->id)
            ->where('bulan', $bulanIni)
            ->where('tahun', $tahunIni)
            ->count();

        // Total pelanggan belum dibaca bulan ini
        $totalPelanggan   = Pelanggan::where('status', 'aktif')->count();
        $sudahInputMeteran = MeteranAir::where('bulan', $bulanIni)->where('tahun', $tahunIni)->count();
        $belumInputMeteran = max(0, $totalPelanggan - $sudahInputMeteran);

        // Pengaduan perlu diproses
        $pengaduanBaru = Pengaduan::where('status', 'baru')->count();

        // 5 meteran terbaru oleh petugas ini
        $meteranTerbaru = MeteranAir::with('pelanggan')
            ->where('petugas_id', $petugas?->id)
            ->latest()
            ->take(6)
            ->get();

        // Chart pemakaian harian bulan ini
        $chartHari = [];
        $chartPemakaian = [];
        for ($d = 1; $d <= now()->day; $d++) {
            $chartHari[] = $d;
            $chartPemakaian[] = MeteranAir::whereDay('tanggal_baca', $d)
                ->whereMonth('tanggal_baca', $bulanIni)
                ->whereYear('tanggal_baca', $tahunIni)
                ->sum('pemakaian');
        }

        return view('petugas.dashboard', compact(
            'meteranBulanIni', 'totalPelanggan', 'sudahInputMeteran',
            'belumInputMeteran', 'pengaduanBaru', 'meteranTerbaru',
            'chartHari', 'chartPemakaian'
        ));
    }
}