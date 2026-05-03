<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TagihanAir;
use App\Models\Pembayaran;
use App\Models\MeteranAir;
use App\Models\Pelanggan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index()
    {
        return view('admin.laporan.index');
    }

    public function tagihan(Request $request)
    {
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);

        $tagihan = TagihanAir::with('pelanggan')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->orderBy('pelanggan_id')
            ->get();

        $summary = [
            'total'       => $tagihan->count(),
            'lunas'       => $tagihan->where('status', 'lunas')->count(),
            'belum_bayar' => $tagihan->whereIn('status', ['belum_bayar', 'terlambat'])->count(),
            'nominal'     => $tagihan->sum('total_tagihan'),
            'terkumpul'   => $tagihan->where('status', 'lunas')->sum('total_tagihan'),
        ];

        return view('admin.laporan.tagihan', compact('tagihan', 'summary', 'bulan', 'tahun'));
    }

    public function pembayaran(Request $request)
    {
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);

        $pembayaran = Pembayaran::with(['pelanggan', 'tagihan'])
            ->whereMonth('tanggal_bayar', $bulan)
            ->whereYear('tanggal_bayar', $tahun)
            ->where('status', 'konfirmasi')
            ->orderBy('tanggal_bayar')
            ->get();

        $summary = [
            'total'   => $pembayaran->count(),
            'nominal' => $pembayaran->sum('jumlah_bayar'),
            'tunai'   => $pembayaran->where('metode_bayar', 'tunai')->sum('jumlah_bayar'),
            'transfer'=> $pembayaran->where('metode_bayar', 'transfer')->sum('jumlah_bayar'),
        ];

        return view('admin.laporan.pembayaran', compact('pembayaran', 'summary', 'bulan', 'tahun'));
    }

    public function pemakaian(Request $request)
    {
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);

        $meteran = MeteranAir::with(['pelanggan', 'petugas'])
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->orderBy('pelanggan_id')
            ->get();

        $summary = [
            'total_pelanggan' => Pelanggan::where('status', 'aktif')->count(),
            'sudah_diinput'   => $meteran->count(),
            'total_volume'    => $meteran->sum('pemakaian'),
            'rata_rata'       => $meteran->count() > 0 ? $meteran->avg('pemakaian') : 0,
        ];

        return view('admin.laporan.pemakaian', compact('meteran', 'summary', 'bulan', 'tahun'));
    }

    // ---- PDF EXPORTS ----

    public function tagihanPdf(Request $request)
    {
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);

        $tagihan = TagihanAir::with('pelanggan')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->orderBy('pelanggan_id')
            ->get();

        $summary = [
            'total'       => $tagihan->count(),
            'lunas'       => $tagihan->where('status', 'lunas')->count(),
            'belum_bayar' => $tagihan->whereIn('status', ['belum_bayar', 'terlambat'])->count(),
            'nominal'     => $tagihan->sum('total_tagihan'),
            'terkumpul'   => $tagihan->where('status', 'lunas')->sum('total_tagihan'),
        ];

        $namaBulan = \App\Services\TagihanService::namaBulan($bulan);

        $pdf = Pdf::loadView('admin.laporan.pdf.tagihan', compact('tagihan', 'summary', 'bulan', 'tahun', 'namaBulan'))
            ->setPaper('a4', 'landscape');

        return $pdf->download("laporan-tagihan-{$namaBulan}-{$tahun}.pdf");
    }

    public function pembayaranPdf(Request $request)
    {
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);

        $pembayaran = Pembayaran::with(['pelanggan', 'tagihan'])
            ->whereMonth('tanggal_bayar', $bulan)
            ->whereYear('tanggal_bayar', $tahun)
            ->where('status', 'konfirmasi')
            ->orderBy('tanggal_bayar')
            ->get();

        $summary = [
            'total'    => $pembayaran->count(),
            'nominal'  => $pembayaran->sum('jumlah_bayar'),
            'tunai'    => $pembayaran->where('metode_bayar', 'tunai')->sum('jumlah_bayar'),
            'transfer' => $pembayaran->where('metode_bayar', 'transfer')->sum('jumlah_bayar'),
            'lainnya'  => $pembayaran->where('metode_bayar', 'lainnya')->sum('jumlah_bayar'),
        ];

        $namaBulan = \App\Services\TagihanService::namaBulan($bulan);

        $pdf = Pdf::loadView('admin.laporan.pdf.pembayaran', compact('pembayaran', 'summary', 'bulan', 'tahun', 'namaBulan'))
            ->setPaper('a4', 'landscape');

        return $pdf->download("laporan-pembayaran-{$namaBulan}-{$tahun}.pdf");
    }
}
