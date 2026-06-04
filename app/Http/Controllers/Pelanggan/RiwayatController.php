<?php
// app/Http/Controllers/Pelanggan/RiwayatController.php
namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\MeteranAir;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    public function index()
    {
        $pelanggan = Auth::user()->pelanggan;

        if (!$pelanggan) {
            abort(403, 'Data pelanggan tidak ditemukan. Hubungi administrator.');
        }

        $riwayatMeteran = MeteranAir::where('pelanggan_id', $pelanggan->id)
            ->with('tagihan')
            ->orderByDesc('tahun')->orderByDesc('bulan')
            ->paginate(12);

        $riwayatBayar = Pembayaran::where('pelanggan_id', $pelanggan->id)
            ->with('tagihan')
            ->latest()
            ->take(10)
            ->get();

        return view('pelanggan.riwayat.index', compact('riwayatMeteran', 'riwayatBayar', 'pelanggan'));
    }
}