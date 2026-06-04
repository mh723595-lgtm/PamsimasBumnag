<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TagihanAir;
use App\Models\Pembayaran;
use App\Models\Pelanggan;
use App\Models\MeteranAir;
use App\Models\Notifikasi;
use App\Models\AktivitasLog;
use App\Services\TagihanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TagihanController extends Controller
{
    protected TagihanService $tagihanService;

    public function __construct(TagihanService $tagihanService)
    {
        $this->tagihanService = $tagihanService;
    }

    public function index(Request $request)
    {
        $query = TagihanAir::with('pelanggan')->orderByDesc('tahun')->orderByDesc('bulan');

        if ($request->filled('status'))   $query->where('status', $request->status);
        if ($request->filled('bulan'))    $query->where('bulan', $request->bulan);
        if ($request->filled('tahun'))    $query->where('tahun', $request->tahun);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) =>
                $q->where('nomor_tagihan', 'like', "%$s%")
                  ->orWhereHas('pelanggan', fn($q2) =>
                      $q2->where('nama_pelanggan','like',"%$s%")
                        ->orWhere('nomor_pelanggan','like',"%$s%"))
            );
        }

        $tagihan = $query->paginate(20)->withQueryString();

        $stats = [
            'belum_bayar' => TagihanAir::where('status','belum_bayar')->count(),
            'lunas'       => TagihanAir::where('status','lunas')->count(),
            'terlambat'   => TagihanAir::where('status','terlambat')->count(),
        ];

        $totalTagihan = TagihanAir::sum('total_tagihan');
        $totalLunas = TagihanAir::where('status', 'lunas')
    ->sum('total_tagihan');
    $totalBelumBayar = TagihanAir::where('status', 'belum_bayar')
    ->sum('total_tagihan');

$totalTerlambat = TagihanAir::where('status', 'terlambat')
    ->sum('total_tagihan');

        $bulanList = range(1, 12);
        $tahunList = range(now()->year, now()->year - 3);

        return view('admin.tagihan.index', compact('tagihan','stats','bulanList','tahunList', 'totalTagihan', 'totalLunas', 'totalBelumBayar', 'totalTerlambat'));
    }

    public function generate(Request $request, Pelanggan $pelanggan)
    {
        $request->validate([
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2020|max:' . (now()->year + 1),
        ], [
            'bulan.required' => 'Bulan wajib dipilih.',
            'bulan.between'  => 'Bulan tidak valid.',
            'tahun.required' => 'Tahun wajib diisi.',
        ]);

        $bulan = (int) $request->bulan;
        $tahun = (int) $request->tahun;

        $existing = TagihanAir::where('pelanggan_id', $pelanggan->id)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->first();

        if ($existing) {
            return back()->with('error',
                "Tagihan periode {$existing->periodeTeks()} untuk pelanggan {$pelanggan->nama_pelanggan} sudah ada. " .
                "Nomor: {$existing->nomor_tagihan}."
            );
        }

        $meteran = MeteranAir::where('pelanggan_id', $pelanggan->id)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->first();

        if (!$meteran) {
            return back()->with('error',
                "Data meteran untuk pelanggan {$pelanggan->nama_pelanggan} periode " .
                TagihanService::namaBulan($bulan) . " {$tahun} belum diinput. " .
                "Silakan input meteran terlebih dahulu."
            );
        }

        $tagihan = $this->tagihanService->generateDariMeteran($meteran);

        if ($pelanggan->user_id) {
            Notifikasi::kirim(
                $pelanggan->user_id,
                '📋 Tagihan Baru Terbit',
                "Tagihan air periode {$tagihan->periodeTeks()} sebesar " .
                TagihanService::formatRupiah($tagihan->total_tagihan) .
                " telah diterbitkan. Jatuh tempo: " .
                $tagihan->tanggal_jatuh_tempo->format('d/m/Y') . ".",
                'info',
                route('pelanggan.tagihan.show', $tagihan)
            );
        }

        AktivitasLog::catat(
            'generate_tagihan',
            "Generate tagihan manual: {$tagihan->nomor_tagihan} untuk {$pelanggan->nomor_pelanggan}",
            'TagihanAir',
            $tagihan->id
        );

        return redirect()->route('admin.tagihan.show', $tagihan)
            ->with('success',
                "✅ Tagihan {$tagihan->nomor_tagihan} berhasil digenerate untuk {$pelanggan->nama_pelanggan}. " .
                "Total: " . TagihanService::formatRupiah($tagihan->total_tagihan) . "."
            );
    }

    public function show(TagihanAir $tagihan)
    {
        $tagihan->load(['pelanggan','meteran','pembayaran.dikonfirmasiOleh']);
        $rincian = $this->tagihanService->rincianTarif($tagihan->pemakaian);
        return view('admin.tagihan.show', compact('tagihan','rincian'));
    }

    public function edit(TagihanAir $tagihan)
{
    // Proteksi: tagihan lunas tidak bisa diedit
    if ($tagihan->status === 'lunas') {
        return redirect()->route('admin.tagihan.show', $tagihan)
            ->with('error', '🔒 Tagihan ini sudah lunas dan tidak dapat diubah.');
    }

    $tagihan->load('pelanggan');
    return view('admin.tagihan.edit', compact('tagihan'));
}

public function update(Request $request, TagihanAir $tagihan)
{
    // Proteksi: tagihan lunas tidak bisa diubah
    if ($tagihan->status === 'lunas') {
        return redirect()->route('admin.tagihan.show', $tagihan)
            ->with('error', '🔒 Tagihan ini sudah lunas dan tidak dapat diubah.');
    }

    $request->validate([
        'status'        => 'required|in:belum_bayar,lunas,terlambat',
        'metode_bayar'  => 'nullable|in:tunai,transfer,lainnya',
        'tanggal_bayar' => 'nullable|date',
        'catatan'       => 'nullable|string|max:500',
    ]);

    $statusLama = $tagihan->status;
    $tagihan->update(['status' => $request->status]);

    if ($request->status === 'lunas') {
        $pembayaranAda = Pembayaran::where('tagihan_id', $tagihan->id)->first();

        if (!$pembayaranAda) {
            $seq   = Pembayaran::whereDate('created_at', today())->count() + 1;
            $nomor = 'PAY-' . now()->format('Ymd') . '-' . str_pad($seq, 5, '0', STR_PAD_LEFT);

            Pembayaran::create([
                'tagihan_id'        => $tagihan->id,
                'pelanggan_id'      => $tagihan->pelanggan_id,
                'nomor_pembayaran'  => $nomor,
                'jumlah_bayar'      => $tagihan->total_bayar ?: $tagihan->total_tagihan,
                'tanggal_bayar'     => $request->tanggal_bayar ?? now()->toDateString(),
                'metode_bayar'      => $request->metode_bayar ?? 'tunai',
                'status'            => 'konfirmasi',
                'dikonfirmasi_oleh' => Auth::id(),
                'catatan'           => $request->catatan ?? 'Dikonfirmasi manual oleh admin',
            ]);
        } else {
            $pembayaranAda->update([
                'status'            => 'konfirmasi',
                'dikonfirmasi_oleh' => Auth::id(),
            ]);
        }

        if ($tagihan->pelanggan?->user_id) {
            Notifikasi::kirim(
                $tagihan->pelanggan->user_id,
                '✅ Tagihan Lunas',
                "Tagihan {$tagihan->nomor_tagihan} periode {$tagihan->periodeTeks()} sebesar " .
                TagihanService::formatRupiah($tagihan->total_bayar ?: $tagihan->total_tagihan) .
                " telah dikonfirmasi lunas.",
                'success'
            );
        }
    }

    AktivitasLog::catat(
        'update_tagihan',
        "Update status tagihan {$tagihan->nomor_tagihan}: {$statusLama} → {$request->status}",
        'TagihanAir',
        $tagihan->id
    );

    return redirect()->route('admin.tagihan.show', $tagihan)
        ->with('success', 'Status tagihan berhasil diperbarui.' .
            ($request->status === 'lunas' ? ' Record pembayaran otomatis dibuat.' : ''));
}
}