<?php
// app/Http/Controllers/Pelanggan/TagihanController.php
namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\TagihanAir;
use App\Services\TagihanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TagihanController extends Controller
{
    public function index()
    {
        $pelanggan = Auth::user()->pelanggan;

        if (!$pelanggan) {
            abort(403, 'Data pelanggan tidak ditemukan. Hubungi administrator.');
        }

        $tagihan = TagihanAir::where('pelanggan_id', $pelanggan->id)
            ->orderByDesc('tahun')->orderByDesc('bulan')
            ->paginate(12);

        $totalBelumBayar = TagihanAir::where('pelanggan_id', $pelanggan->id)
            ->whereIn('status', ['belum_bayar', 'terlambat'])
            ->sum('total_bayar');

        $totalLunas = TagihanAir::where('pelanggan_id', $pelanggan->id)
            ->where('status', 'lunas')->sum('total_bayar');

        return view('pelanggan.tagihan.index', compact('tagihan', 'totalBelumBayar', 'totalLunas'));
    }

    public function show(TagihanAir $tagihan)
    {
        $pelanggan = Auth::user()->pelanggan;

        if (!$pelanggan) {
            abort(403, 'Data pelanggan tidak ditemukan. Hubungi administrator.');
        }

        if ($tagihan->pelanggan_id !== $pelanggan->id) {
            abort(403, 'Anda tidak berhak mengakses tagihan ini.');
        }

        $tagihan->load(['meteran', 'pembayaran']);
        $tagihanService = app(TagihanService::class);
        $rincian = $tagihanService->rincianTarif((float) $tagihan->pemakaian);
        $hasil   = $tagihanService->hitungTagihan((float) $tagihan->pemakaian);

        return view('pelanggan.tagihan.show', compact('tagihan', 'rincian', 'hasil'));
    }
}