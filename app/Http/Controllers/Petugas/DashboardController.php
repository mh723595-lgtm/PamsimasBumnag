<?php
// app/Http/Controllers/Petugas/DashboardController.php
namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\MeteranAir;
use App\Models\Pelanggan;
use App\Models\Pengaduan;
use App\Models\TagihanAir;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $petugas  = Auth::user()->petugas;
        $bulanIni = now()->month;
        $tahunIni = now()->year;
        $hariIni  = now()->day;

        // Stat cards
        $meteranBulanIni = MeteranAir::where('petugas_id', $petugas?->id)
            ->where('bulan', $bulanIni)
            ->where('tahun', $tahunIni)
            ->count();

        $totalPelanggan    = Pelanggan::where('status', 'aktif')->count();
        $sudahInputMeteran = MeteranAir::where('bulan', $bulanIni)->where('tahun', $tahunIni)->count();
        $belumInputMeteran = max(0, $totalPelanggan - $sudahInputMeteran);

        $pengaduanBaru = Pengaduan::where('status', 'baru')->count();

        // 6 meteran terbaru oleh petugas ini
        $meteranTerbaru = MeteranAir::with('pelanggan')
            ->where('petugas_id', $petugas?->id)
            ->latest()
            ->take(6)
            ->get();

        // ── Chart pemakaian harian bulan ini: SATU query, group by hari ──────
        $pemakaianHarianRaw = MeteranAir::whereMonth('tanggal_baca', $bulanIni)
            ->whereYear('tanggal_baca', $tahunIni)
            ->selectRaw('DAY(tanggal_baca) as hari, SUM(pemakaian) as total')
            ->groupByRaw('DAY(tanggal_baca)')
            ->get()
            ->keyBy('hari');

        $chartHari      = [];
        $chartPemakaian = [];

        for ($d = 1; $d <= $hariIni; $d++) {
            $chartHari[]      = $d;
            $chartPemakaian[] = (float) ($pemakaianHarianRaw[$d]->total ?? 0);
        }

        return view('petugas.dashboard', compact(
            'meteranBulanIni', 'totalPelanggan', 'sudahInputMeteran',
            'belumInputMeteran', 'pengaduanBaru', 'meteranTerbaru',
            'chartHari', 'chartPemakaian'
        ));
    }
}