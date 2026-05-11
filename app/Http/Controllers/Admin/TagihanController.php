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

    public function show(TagihanAir $tagihan)
    {
        $tagihan->load(['pelanggan','meteran','pembayaran.dikonfirmasiOleh']);
        $rincian = $this->tagihanService->rincianTarif($tagihan->pemakaian);
        return view('admin.tagihan.show', compact('tagihan','rincian'));
    }

    public function edit(TagihanAir $tagihan)
    {
        $tagihan->load('pelanggan');
        return view('admin.tagihan.edit', compact('tagihan'));
    }

    /**
     * FIX: Update status tagihan.
     * Jika diubah ke 'lunas' dan belum ada record Pembayaran → auto-create Pembayaran konfirmasi.
     * Jika sudah ada Pembayaran, update statusnya.
     */
    public function update(Request $request, TagihanAir $tagihan)
    {
        $request->validate([
            'status'        => 'required|in:belum_bayar,lunas,terlambat',
            'metode_bayar'  => 'nullable|in:tunai,transfer,lainnya',
            'tanggal_bayar' => 'nullable|date',
            'catatan'       => 'nullable|string|max:500',
        ]);

        $statusLama = $tagihan->status;
        $tagihan->update(['status' => $request->status]);

        // FIX: Jika diubah ke LUNAS — create atau update record Pembayaran
        if ($request->status === 'lunas') {
            $pembayaranAda = Pembayaran::where('tagihan_id', $tagihan->id)->first();

            if (!$pembayaranAda) {
                // Generate nomor pembayaran
                $seq    = Pembayaran::whereDate('created_at', today())->count() + 1;
                $nomor  = 'PAY-' . now()->format('Ymd') . '-' . str_pad($seq, 5, '0', STR_PAD_LEFT);

                Pembayaran::create([
                    'tagihan_id'       => $tagihan->id,
                    'pelanggan_id'     => $tagihan->pelanggan_id,
                    'nomor_pembayaran' => $nomor,
                    'jumlah_bayar'     => $tagihan->total_bayar ?: $tagihan->total_tagihan,
                    'tanggal_bayar'    => $request->tanggal_bayar ?? now()->toDateString(),
                    'metode_bayar'     => $request->metode_bayar ?? 'tunai',
                    'status'           => 'konfirmasi',
                    'dikonfirmasi_oleh'=> Auth::id(),
                    'catatan'          => $request->catatan ?? 'Dikonfirmasi manual oleh admin',
                ]);
            } else {
                // Update pembayaran yang sudah ada ke konfirmasi
                $pembayaranAda->update([
                    'status'           => 'konfirmasi',
                    'dikonfirmasi_oleh'=> Auth::id(),
                ]);
            }

            // Kirim notifikasi ke pelanggan
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

        // Jika diubah DARI lunas ke status lain — update pembayaran ke pending
        if ($statusLama === 'lunas' && $request->status !== 'lunas') {
            Pembayaran::where('tagihan_id', $tagihan->id)
                ->where('status','konfirmasi')
                ->update(['status' => 'pending']);
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
            return back()->with('error', 'Data meteran belum ada. Silakan input meteran terlebih dahulu.');
        }

        $tagihan = $this->tagihanService->generateDariMeteran($meteran);
        AktivitasLog::catat('generate_tagihan', "Generate tagihan {$tagihan->nomor_tagihan}", 'TagihanAir', $tagihan->id);

        return redirect()->route('admin.tagihan.show', $tagihan)->with('success', 'Tagihan berhasil digenerate.');
    }
}