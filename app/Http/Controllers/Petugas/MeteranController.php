<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\MeteranAir;
use App\Models\Pelanggan;
use App\Models\AktivitasLog;
use App\Services\TagihanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MeteranController extends Controller
{
    protected TagihanService $tagihanService;

    public function __construct(TagihanService $tagihanService)
    {
        $this->tagihanService = $tagihanService;
    }

    public function index(Request $request)
    {
        $petugas = Auth::user()->petugas;
        $bulan   = $request->get('bulan', now()->month);
        $tahun   = $request->get('tahun', now()->year);

        // Semua pelanggan aktif
        $pelangganList = Pelanggan::where('status', 'aktif')
            ->with(['meteranAir' => fn($q) => $q->where('bulan', $bulan)->where('tahun', $tahun)])
            ->orderBy('nomor_pelanggan')
            ->get();

        // Yang sudah diinput bulan ini
        $sudahInput = MeteranAir::where('bulan', $bulan)->where('tahun', $tahun)
            ->pluck('pelanggan_id')->toArray();

        return view('petugas.meteran.index', compact(
            'pelangganList', 'sudahInput', 'bulan', 'tahun'
        ));
    }

    public function create(Request $request)
    {
        $pelangganList = Pelanggan::where('status', 'aktif')->orderBy('nomor_pelanggan')->get();
        $selectedPelanggan = null;

        if ($request->filled('pelanggan_id')) {
            $selectedPelanggan = Pelanggan::find($request->pelanggan_id);
        }

        return view('petugas.meteran.create', compact('pelangganList', 'selectedPelanggan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pelanggan_id'  => 'required|exists:pelanggans,id',
            'bulan'         => 'required|integer|between:1,12',
            'tahun'         => 'required|integer|min:2020',
            'angka_akhir'   => 'required|numeric|min:0',
            'tanggal_baca'  => 'required|date|before_or_equal:today',
            'keterangan'    => 'nullable|string|max:500',
        ], [
            'pelanggan_id.required' => 'Pilih pelanggan.',
            'angka_akhir.required'  => 'Angka akhir meteran wajib diisi.',
            'angka_akhir.numeric'   => 'Angka meteran harus berupa angka.',
            'tanggal_baca.required' => 'Tanggal baca wajib diisi.',
        ]);

        // Cek duplikat
        $existing = MeteranAir::where('pelanggan_id', $request->pelanggan_id)
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->first();

        if ($existing) {
            return back()->withErrors(['bulan' => 'Meteran untuk periode ini sudah diinput.'])->withInput();
        }

        // Cari angka awal dari meteran sebelumnya
        $meteranSebelumnya = MeteranAir::where('pelanggan_id', $request->pelanggan_id)
            ->where(function ($q) use ($request) {
                $q->where('tahun', '<', $request->tahun)
                  ->orWhere(fn($q2) =>
                    $q2->where('tahun', $request->tahun)->where('bulan', '<', $request->bulan)
                  );
            })
            ->orderByDesc('tahun')->orderByDesc('bulan')
            ->first();

        $pelanggan  = Pelanggan::findOrFail($request->pelanggan_id);
        $angkaAwal  = $meteranSebelumnya ? $meteranSebelumnya->angka_akhir : $pelanggan->meteran_awal;
        $angkaAkhir = $request->angka_akhir;

        if ($angkaAkhir < $angkaAwal) {
            return back()->withErrors(['angka_akhir' => "Angka akhir ({$angkaAkhir}) tidak boleh lebih kecil dari angka awal ({$angkaAwal})."]);
        }

        $petugas = Auth::user()->petugas;

        $meteran = MeteranAir::create([
            'pelanggan_id'  => $request->pelanggan_id,
            'petugas_id'    => $petugas?->id,
            'bulan'         => $request->bulan,
            'tahun'         => $request->tahun,
            'angka_awal'    => $angkaAwal,
            'angka_akhir'   => $angkaAkhir,
            'pemakaian'     => $angkaAkhir - $angkaAwal,
            'tanggal_baca'  => $request->tanggal_baca,
            'keterangan'    => $request->keterangan,
        ]);

        // Auto-generate tagihan
        $tagihan = $this->tagihanService->generateDariMeteran($meteran);

        AktivitasLog::catat(
            'input_meteran',
            "Input meteran {$pelanggan->nomor_pelanggan} bulan {$request->bulan}/{$request->tahun}, pemakaian: {$meteran->pemakaian} m³",
            'MeteranAir', $meteran->id
        );

        return redirect()->route('petugas.meteran.show', $meteran)
            ->with('success', "Meteran berhasil diinput! Pemakaian: {$meteran->pemakaian} m³ — Tagihan: " . \App\Services\TagihanService::formatRupiah((float) ($tagihan->total_tagihan ?? 0)));
    }

    public function show(MeteranAir $meteranAir)
    {
        $meteranAir->load(['pelanggan', 'petugas', 'tagihan']);
        $rincian = null;
        if ($meteranAir->tagihan) {
            $tagihanService = app(TagihanService::class);
            $rincian = $tagihanService->rincianTarif((float) ($tagihan->pemakaian ?? 0));
        }
        return view('petugas.meteran.show', compact('meteranAir', 'rincian'));
    }
}