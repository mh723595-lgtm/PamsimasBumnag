<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\MeteranAir;
use App\Models\Pelanggan;
use App\Models\AktivitasLog;
use App\Services\TagihanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\SettingAplikasi;

class MeteranController extends Controller
{
    protected TagihanService $tagihanService;

    public function __construct(TagihanService $tagihanService)
    {
        $this->tagihanService = $tagihanService;
    }

    public function index(Request $request)
    {
        $bulan  = (int) $request->input('bulan', now()->month);
        $tahun  = (int) $request->input('tahun', now()->year);
        $petugas = Auth::user()->petugas;

        // Ambil jorong yang ditugaskan ke petugas ini
        $jorongIds = \App\Models\AssignPetugas::where('petugas_id', $petugas?->id)
            ->where('aktif', true)
            ->pluck('jorong_id');

        $pelangganList = Pelanggan::where('status', 'aktif')
            ->whereIn('jorong_id', $jorongIds)
            ->with([
                'meteranAir' => fn($q) => $q->where('bulan', $bulan)->where('tahun', $tahun),
                'jorong',
            ])
            ->orderBy('nomor_pelanggan')
            ->get();

        $sudahInput = MeteranAir::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->pluck('pelanggan_id')
            ->toArray();

        $jorongList = \App\Models\Jorong::whereIn('id', $jorongIds)->get();

        return view('petugas.meteran.index', compact(
            'pelangganList',
            'sudahInput',
            'bulan',
            'tahun',
            'jorongList',
            'jorongIds'
        ));
    }

    public function create(Request $request)
    {
        $petugas = Auth::user()->petugas;
        $jorongIds = \App\Models\AssignPetugas::where('petugas_id', $petugas?->id)
            ->where('aktif', true)
            ->pluck('jorong_id');

        $pelangganList     = Pelanggan::where('status', 'aktif')
            ->whereIn('jorong_id', $jorongIds)
            ->orderBy('nomor_pelanggan')
            ->get();
        $selectedPelanggan = null;
        $angkaAwalRef      = 0;

        if ($request->filled('pelanggan_id')) {
            $selectedPelanggan = Pelanggan::find($request->pelanggan_id);

            if ($selectedPelanggan) {
                $bulan = (int) $request->input('bulan', now()->month);
                $tahun = (int) $request->input('tahun', now()->year);

                $meteranSebelumnya = MeteranAir::where('pelanggan_id', $selectedPelanggan->id)
                    ->where(function ($q) use ($bulan, $tahun) {
                        $q->where('tahun', '<', $tahun)
                            ->orWhere(
                                fn($q2) =>
                                $q2->where('tahun', $tahun)->where('bulan', '<', $bulan)
                            );
                    })
                    ->orderByDesc('tahun')
                    ->orderByDesc('bulan')
                    ->first();

                $angkaAwalRef = $meteranSebelumnya
                    ? (float) $meteranSebelumnya->angka_akhir
                    : (float) $selectedPelanggan->meteran_awal;
            }
        }

        $tarifConfig = [
            'tarif_blok1' => (float) SettingAplikasi::get('tarif_blok1', 20000),
            'tarif_blok2' => (float) SettingAplikasi::get('tarif_blok2', 1500),
            'tarif_blok3' => (float) SettingAplikasi::get('tarif_blok3', 2000),
            'biaya_admin' => (float) SettingAplikasi::get('biaya_admin', 0),
        ];

        return view('petugas.meteran.create', compact(
            'pelangganList',
            'selectedPelanggan',
            'angkaAwalRef',
            'tarifConfig'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'bulan'        => 'required|integer|between:1,12',
            'tahun'        => 'required|integer|min:2020|max:' . (now()->year + 1),
            'angka_akhir'  => 'required|numeric|min:0',
            'tanggal_baca' => 'required|date|before_or_equal:today',
            'keterangan'   => 'nullable|string|max:500',
            'foto_meter'   => 'nullable|image|mimes:jpg,jpeg,png,webp,heic|max:5120',
        ], [
            'pelanggan_id.required'        => 'Pilih pelanggan terlebih dahulu.',
            'angka_akhir.required'         => 'Angka akhir meteran wajib diisi.',
            'angka_akhir.numeric'          => 'Angka meteran harus berupa angka.',
            'tanggal_baca.required'        => 'Tanggal baca wajib diisi.',
            'tanggal_baca.before_or_equal' => 'Tanggal baca tidak boleh melewati hari ini.',
            'foto_meter.image'             => 'File harus berupa gambar.',
            'foto_meter.max'               => 'Ukuran foto maksimal 5MB.',
        ]);

        // Cek duplikat
        $existing = MeteranAir::where('pelanggan_id', $request->pelanggan_id)
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->first();

        if ($existing) {
            return back()
                ->withErrors(['bulan' => 'Meteran untuk periode ' . TagihanService::namaBulan($request->bulan) . ' ' . $request->tahun . ' sudah diinput.'])
                ->withInput();
        }

        $pelanggan = Pelanggan::findOrFail($request->pelanggan_id);

        // Hitung angka awal dari bulan sebelumnya
        $meteranSebelumnya = MeteranAir::where('pelanggan_id', $request->pelanggan_id)
            ->where(function ($q) use ($request) {
                $q->where('tahun', '<', $request->tahun)
                    ->orWhere(
                        fn($q2) =>
                        $q2->where('tahun', $request->tahun)->where('bulan', '<', $request->bulan)
                    );
            })
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->first();

        $angkaAwal  = $meteranSebelumnya ? (float) $meteranSebelumnya->angka_akhir : (float) $pelanggan->meteran_awal;
        $angkaAkhir = (float) $request->angka_akhir;

        if ($angkaAkhir < $angkaAwal) {
            return back()
                ->withErrors(['angka_akhir' => "Angka akhir ({$angkaAkhir}) tidak boleh lebih kecil dari angka awal ({$angkaAwal})."])
                ->withInput();
        }

        // Upload foto meter (opsional)
        $fotoPath = null;
        if ($request->hasFile('foto_meter') && $request->file('foto_meter')->isValid()) {
            $fotoPath = $request->file('foto_meter')->store('meteran', 'public');
        }

        $petugas = Auth::user()->petugas;

        $meteran = MeteranAir::create([
            'pelanggan_id' => $request->pelanggan_id,
            'petugas_id'   => $petugas?->id,
            'bulan'        => $request->bulan,
            'tahun'        => $request->tahun,
            'angka_awal'   => $angkaAwal,
            'angka_akhir'  => $angkaAkhir,
            'pemakaian'    => $angkaAkhir - $angkaAwal,
            'tanggal_baca' => $request->tanggal_baca,
            'keterangan'   => $request->keterangan,
            'foto_meter'   => $fotoPath,
        ]);

        // Auto-generate tagihan
        $tagihan = $this->tagihanService->generateDariMeteran($meteran);

        AktivitasLog::catat(
            'input_meteran',
            "Input meteran {$pelanggan->nomor_pelanggan} periode {$request->bulan}/{$request->tahun}, pemakaian: {$meteran->pemakaian} m³" .
                ($fotoPath ? ' (+ foto)' : ''),
            'MeteranAir',
            $meteran->id
        );

        return redirect()
            ->route('petugas.meteran.show', $meteran)
            ->with('success', "✅ Meteran berhasil diinput! Pemakaian: {$meteran->pemakaian} m³ — Tagihan: " . TagihanService::formatRupiah((float) $tagihan->total_tagihan));
    }

    public function show(MeteranAir $meteranAir)
    {
        $meteranAir->load(['pelanggan', 'petugas', 'tagihan']);

        $rincian = null;
        if ($meteranAir->tagihan) {
            $rincian = $this->tagihanService->rincianTarif((float) $meteranAir->pemakaian);
        }

        return view('petugas.meteran.show', compact('meteranAir', 'rincian'));
    }
}
