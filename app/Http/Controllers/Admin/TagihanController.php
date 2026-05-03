<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TagihanAir;
use App\Models\Pelanggan;
use App\Models\MeteranAir;
use App\Models\AktivitasLog;
use App\Services\TagihanService;
use Illuminate\Http\Request;

class TagihanController extends Controller
{
    protected TagihanService $tagihanService;

    public function __construct(TagihanService $tagihanService)
    {
        $this->tagihanService = $tagihanService;
    }

    public function index(Request $request)
    {
        $query = TagihanAir::with('pelanggan')
            ->orderByDesc('created_at');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('pelanggan', fn($q) =>
                $q->where('nama_pelanggan', 'like', "%$s%")
                  ->orWhere('nomor_pelanggan', 'like', "%$s%")
            )->orWhere('nomor_tagihan', 'like', "%$s%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        $tagihan = $query->paginate(15)->withQueryString();

        $totalTagihan      = TagihanAir::count();
        $totalLunas        = TagihanAir::where('status', 'lunas')->count();
        $totalBelumBayar   = TagihanAir::whereIn('status', ['belum_bayar', 'terlambat'])->count();
        $totalNominal      = TagihanAir::sum('total_tagihan');

        return view('admin.tagihan.index', compact(
            'tagihan', 'totalTagihan', 'totalLunas', 'totalBelumBayar', 'totalNominal'
        ));
    }

    public function show(TagihanAir $tagihan)
    {
        $tagihan->load(['pelanggan.user', 'meteran.petugas', 'pembayaran.dikonfirmasiOleh']);
        $rincian = $this->tagihanService->rincianTarif((float) $tagihan->pemakaian);
        $hasil   = $this->tagihanService->hitungTagihan((float) $tagihan->pemakaian);

        return view('admin.tagihan.show', compact('tagihan', 'rincian', 'hasil'));
    }

    public function edit(TagihanAir $tagihan)
    {
        $tagihan->load('pelanggan');
        return view('admin.tagihan.edit', compact('tagihan'));
    }

    public function update(Request $request, TagihanAir $tagihan)
    {
        $request->validate([
            'status' => 'required|in:belum_bayar,lunas,terlambat',
        ]);

        $tagihan->update(['status' => $request->status]);

        AktivitasLog::catat('update_tagihan', "Update status tagihan {$tagihan->nomor_tagihan} menjadi {$request->status}", 'TagihanAir', $tagihan->id);

        return redirect()->route('admin.tagihan.index')->with('success', 'Status tagihan berhasil diperbarui.');
    }

    public function destroy(TagihanAir $tagihan)
    {
        if ($tagihan->status === 'lunas') {
            return back()->with('error', 'Tagihan yang sudah lunas tidak dapat dihapus.');
        }

        AktivitasLog::catat('delete_tagihan', "Hapus tagihan {$tagihan->nomor_tagihan}", 'TagihanAir', $tagihan->id);
        $tagihan->delete();

        return redirect()->route('admin.tagihan.index')->with('success', 'Tagihan berhasil dihapus.');
    }

    public function generate(Request $request, Pelanggan $pelanggan)
    {
        $request->validate([
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2020',
        ]);

        $meteran = MeteranAir::where('pelanggan_id', $pelanggan->id)
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->first();

        if (!$meteran) {
            return back()->with('error', 'Data meteran untuk periode tersebut belum ada. Silakan input meteran terlebih dahulu.');
        }

        $tagihan = $this->tagihanService->generateDariMeteran($meteran);
        AktivitasLog::catat('generate_tagihan', "Generate tagihan {$tagihan->nomor_tagihan}", 'TagihanAir', $tagihan->id);

        return redirect()->route('admin.tagihan.show', $tagihan)->with('success', 'Tagihan berhasil digenerate.');
    }
}
