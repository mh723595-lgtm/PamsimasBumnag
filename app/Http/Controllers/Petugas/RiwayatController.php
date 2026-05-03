<?php
// app/Http/Controllers/Petugas/RiwayatController.php
namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\MeteranAir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $petugas = Auth::user()->petugas;

        $query = MeteranAir::with(['pelanggan', 'tagihan'])
            ->where('petugas_id', $petugas?->id)
            ->orderByDesc('created_at');

        if ($request->filled('bulan')) $query->where('bulan', $request->bulan);
        if ($request->filled('tahun'))  $query->where('tahun', $request->tahun);

        $riwayat = $query->paginate(15)->withQueryString();

        return view('petugas.riwayat.index', compact('riwayat'));
    }
}
