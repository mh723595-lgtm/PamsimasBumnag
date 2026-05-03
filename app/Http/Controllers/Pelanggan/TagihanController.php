<?php
// app/Http/Controllers/Pelanggan/TagihanController.php
namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\TagihanAir;
use App\Services\TagihanService;
use Illuminate\Http\Request;

class TagihanController extends Controller
{
    public function index()
    {
        $pelanggan = auth()->user()->pelanggan;

        $tagihan = TagihanAir::where('pelanggan_id', $pelanggan->id)
            ->orderByDesc('tahun')->orderByDesc('bulan')
            ->paginate(12);

        $totalBelumBayar = TagihanAir::where('pelanggan_id', $pelanggan->id)
            ->whereIn('status', ['belum_bayar', 'terlambat'])
            ->sum('total_tagihan');

        $totalLunas = TagihanAir::where('pelanggan_id', $pelanggan->id)
            ->where('status', 'lunas')->sum('total_tagihan');

        return view('pelanggan.tagihan.index', compact('tagihan', 'totalBelumBayar', 'totalLunas'));
    }

    public function show(TagihanAir $tagihan)
    {
        $pelanggan = auth()->user()->pelanggan;

        // Guard: hanya bisa lihat tagihan milik sendiri
        if ($tagihan->pelanggan_id !== $pelanggan->id) {
            abort(403);
        }

        $tagihan->load(['meteran', 'pembayaran']);
        $tagihanService = app(TagihanService::class);
        $rincian = $tagihanService->rincianTarif($tagihan->pemakaian);
        $hasil   = $tagihanService->hitungTagihan($tagihan->pemakaian);

        return view('pelanggan.tagihan.show', compact('tagihan', 'rincian', 'hasil'));
    }
}
