<?php
// app/Http/Controllers/Pelanggan/DashboardController.php
namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\TagihanAir;
use App\Models\MeteranAir;
use App\Models\Pembayaran;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $pelanggan = Auth::user()->pelanggan;

        if (!$pelanggan) {
            return redirect('/')->with('error', 'Data pelanggan tidak ditemukan.');
        }

        // Tagihan belum bayar
        $tagihanBelumBayar = TagihanAir::where('pelanggan_id', $pelanggan->id)
            ->whereIn('status', ['belum_bayar', 'terlambat'])
            ->latest()
            ->get();

        // Tagihan bulan ini
        $tagihanBulanIni = TagihanAir::where('pelanggan_id', $pelanggan->id)
            ->where('bulan', now()->month)
            ->where('tahun', now()->year)
            ->first();

        // Total tagihan 3 bulan terakhir
        $totalTagihan3Bulan = TagihanAir::where('pelanggan_id', $pelanggan->id)
            ->where('created_at', '>=', now()->subMonths(3))
            ->sum('total_tagihan');

        // Riwayat pembayaran terbaru
        $riwayatPembayaran = Pembayaran::where('pelanggan_id', $pelanggan->id)
            ->with('tagihan')
            ->latest()
            ->take(5)
            ->get();

        // Chart pemakaian 6 bulan
        $chartLabel = [];
        $chartPemakaian = [];
        for ($i = 5; $i >= 0; $i--) {
            $dt = Carbon::now()->subMonths($i);
            $chartLabel[] = $dt->translatedFormat('M Y');
            $m = MeteranAir::where('pelanggan_id', $pelanggan->id)
                ->where('bulan', $dt->month)->where('tahun', $dt->year)
                ->first();
            $chartPemakaian[] = $m ? $m->pemakaian : 0;
        }

        // Meteran terakhir
        $meteranTerakhir = MeteranAir::where('pelanggan_id', $pelanggan->id)->latest()->first();

        return view('pelanggan.dashboard', compact(
            'pelanggan', 'tagihanBelumBayar', 'tagihanBulanIni',
            'totalTagihan3Bulan', 'riwayatPembayaran',
            'chartLabel', 'chartPemakaian', 'meteranTerakhir'
        ));
    }
}