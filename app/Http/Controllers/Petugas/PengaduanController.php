<?php
// app/Http/Controllers/Petugas/PengaduanController.php
namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use App\Models\AktivitasLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengaduanController extends Controller
{
    public function index()
    {
        $pengaduan = Pengaduan::with('pelanggan')
            ->orderByRaw("FIELD(status,'baru','diproses','selesai','ditolak')")
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('petugas.pengaduan.index', compact('pengaduan'));
    }

    public function show(Pengaduan $pengaduan)
    {
        $pengaduan->load(['pelanggan', 'ditanganiOleh']);
        return view('petugas.pengaduan.show', compact('pengaduan'));
    }

    public function proses(Request $request, Pengaduan $pengaduan)
    {
        $request->validate([
            'status' => 'required|in:diproses,selesai',
            'catatan'=> 'nullable|string|max:500',
        ]);

        $pengaduan->update([
            'status'         => $request->status,
            'tanggapan'      => $request->catatan,
            'ditangani_oleh' => Auth::id(),
            'tanggal_selesai'=> $request->status === 'selesai' ? now() : null,
        ]);

        AktivitasLog::catat('proses_pengaduan', "Petugas memproses pengaduan {$pengaduan->nomor_pengaduan}", 'Pengaduan', $pengaduan->id);

        return redirect()->route('petugas.pengaduan.index')->with('success', 'Pengaduan berhasil diperbarui.');
    }
}