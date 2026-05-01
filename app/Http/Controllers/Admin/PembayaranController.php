<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\TagihanAir;
use App\Models\Notifikasi;
use App\Models\AktivitasLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $query = Pembayaran::with(['pelanggan', 'tagihan'])->orderByDesc('created_at');

        if ($request->filled('status'))  $query->where('status', $request->status);
        if ($request->filled('metode'))  $query->where('metode_bayar', $request->metode);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) =>
                $q->where('nomor_pembayaran', 'like', "%$s%")
                  ->orWhereHas('pelanggan', fn($q2) => $q2->where('nama_pelanggan', 'like', "%$s%"))
            );
        }

        $pembayaran = $query->paginate(15)->withQueryString();

        $stats = [
            'pending'    => Pembayaran::where('status','pending')->count(),
            'konfirmasi' => Pembayaran::where('status','konfirmasi')->count(),
            'ditolak'    => Pembayaran::where('status','ditolak')->count(),
            'total'      => Pembayaran::where('status','konfirmasi')->sum('jumlah_bayar'),
        ];

        return view('admin.pembayaran.index', compact('pembayaran', 'stats'));
    }

    public function show(Pembayaran $pembayaran)
    {
        $pembayaran->load(['pelanggan', 'tagihan.meteran', 'dikonfirmasiOleh']);
        return view('admin.pembayaran.show', compact('pembayaran'));
    }

    public function konfirmasi(Request $request, TagihanAir $tagihan)
    {
        $request->validate([
            'jumlah_bayar' => 'required|numeric|min:1',
            'tanggal_bayar'=> 'required|date',
            'metode_bayar' => 'required|in:tunai,transfer,lainnya',
            'catatan'      => 'nullable|string|max:500',
        ]);

        // Cek sudah ada pembayaran
        if ($tagihan->pembayaran && $tagihan->pembayaran->status === 'konfirmasi') {
            return back()->with('error', 'Tagihan ini sudah dikonfirmasi pembayarannya.');
        }

        $nomorPembayaran = 'PAY-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));

        $pembayaran = Pembayaran::create([
            'tagihan_id'       => $tagihan->id,
            'pelanggan_id'     => $tagihan->pelanggan_id,
            'nomor_pembayaran' => $nomorPembayaran,
            'jumlah_bayar'     => $request->jumlah_bayar,
            'tanggal_bayar'    => $request->tanggal_bayar,
            'metode_bayar'     => $request->metode_bayar,
            'status'           => 'konfirmasi',
            'dikonfirmasi_oleh'=> Auth::id(),
            'catatan'          => $request->catatan,
        ]);

        // Update status tagihan
        $tagihan->update(['status' => 'lunas']);

        // Notifikasi pelanggan
        Notifikasi::create([
            'user_id'     => $tagihan->pelanggan->user_id,
            'judul'       => 'Pembayaran Dikonfirmasi',
            'pesan'       => "Pembayaran tagihan {$tagihan->nomor_tagihan} sebesar Rp " . number_format($request->jumlah_bayar, 0, ',', '.') . " telah dikonfirmasi.",
            'tipe'        => 'success',
            'sudah_dibaca'=> false,
        ]);

        AktivitasLog::catat('konfirmasi_pembayaran', "Konfirmasi pembayaran {$nomorPembayaran} untuk tagihan {$tagihan->nomor_tagihan}", 'Pembayaran', $pembayaran->id);

        return redirect()->route('admin.tagihan.show', $tagihan)
            ->with('success', "Pembayaran {$nomorPembayaran} berhasil dikonfirmasi. Tagihan status diubah ke Lunas.");
    }

    public function update(Request $request, Pembayaran $pembayaran)
    {
        $request->validate([
            'status' => 'required|in:pending,konfirmasi,ditolak',
        ]);

        $old = $pembayaran->status;
        $pembayaran->update([
            'status'           => $request->status,
            'dikonfirmasi_oleh'=> Auth::id(),
        ]);

        if ($request->status === 'konfirmasi' && $old !== 'konfirmasi') {
            $pembayaran->tagihan->update(['status' => 'lunas']);
        } elseif ($request->status === 'ditolak' && $old === 'konfirmasi') {
            $pembayaran->tagihan->update(['status' => 'belum_bayar']);
        }

        return redirect()->route('admin.pembayaran.show', $pembayaran)
            ->with('success', 'Status pembayaran diperbarui.');
    }

    public function destroy(Pembayaran $pembayaran)
    {
        if ($pembayaran->status === 'konfirmasi') {
            $pembayaran->tagihan->update(['status' => 'belum_bayar']);
        }
        $pembayaran->delete();

        return redirect()->route('admin.pembayaran.index')
            ->with('success', 'Data pembayaran dihapus.');
    }
}
