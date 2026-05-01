<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use App\Models\Notifikasi;
use App\Models\AktivitasLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengaduanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pengaduan::with('pelanggan')->orderByDesc('created_at');

        if ($request->filled('status'))   $query->where('status', $request->status);
        if ($request->filled('jenis'))    $query->where('jenis', $request->jenis);
        if ($request->filled('prioritas'))$query->where('prioritas', $request->prioritas);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) =>
                $q->where('judul', 'like', "%$s%")
                  ->orWhere('nomor_pengaduan', 'like', "%$s%")
                  ->orWhereHas('pelanggan', fn($q2) => $q2->where('nama_pelanggan', 'like', "%$s%"))
            );
        }

        $pengaduan = $query->paginate(15)->withQueryString();

        $stats = [
            'baru'     => Pengaduan::where('status','baru')->count(),
            'diproses' => Pengaduan::where('status','diproses')->count(),
            'selesai'  => Pengaduan::where('status','selesai')->count(),
            'ditolak'  => Pengaduan::where('status','ditolak')->count(),
        ];

        return view('admin.pengaduan.index', compact('pengaduan', 'stats'));
    }

    public function show(Pengaduan $pengaduan)
    {
        $pengaduan->load(['pelanggan.user', 'ditanganiOleh']);
        return view('admin.pengaduan.show', compact('pengaduan'));
    }

    public function update(Request $request, Pengaduan $pengaduan)
    {
        $request->validate([
            'status'    => 'required|in:baru,diproses,selesai,ditolak',
            'tanggapan' => 'required_if:status,selesai,ditolak|nullable|string|max:1000',
            'prioritas' => 'required|in:rendah,sedang,tinggi',
        ]);

        $pengaduan->update([
            'status'         => $request->status,
            'prioritas'      => $request->prioritas,
            'tanggapan'      => $request->tanggapan,
            'ditangani_oleh' => Auth::id(),
            'tanggal_selesai'=> in_array($request->status, ['selesai','ditolak']) ? now() : null,
        ]);

        // Kirim notifikasi ke pelanggan
        Notifikasi::create([
            'user_id'     => $pengaduan->pelanggan->user_id,
            'judul'       => 'Update Pengaduan: ' . $pengaduan->nomor_pengaduan,
            'pesan'       => "Status pengaduan Anda telah diubah menjadi: " . ucfirst($request->status) . ($request->tanggapan ? ". Tanggapan: {$request->tanggapan}" : ''),
            'tipe'        => $request->status === 'selesai' ? 'success' : 'info',
            'url'         => route('pelanggan.pengaduan.show', $pengaduan),
            'sudah_dibaca'=> false,
        ]);

        AktivitasLog::catat('update_pengaduan', "Update pengaduan {$pengaduan->nomor_pengaduan} → {$request->status}", 'Pengaduan', $pengaduan->id);

        return redirect()->route('admin.pengaduan.show', $pengaduan)
            ->with('success', 'Pengaduan berhasil diperbarui dan notifikasi dikirim ke pelanggan.');
    }

    public function tanggapi(Request $request, Pengaduan $pengaduan)
    {
        return $this->update($request, $pengaduan);
    }
}
