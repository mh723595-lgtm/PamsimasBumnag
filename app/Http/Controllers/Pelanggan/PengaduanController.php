<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use App\Models\AktivitasLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PengaduanController extends Controller
{
    public function index()
    {
        $pelanggan = Auth::user()->pelanggan;

        $pengaduan = Pengaduan::where('pelanggan_id', $pelanggan->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('pelanggan.pengaduan.index', compact('pengaduan'));
    }

    public function create()
    {
        return view('pelanggan.pengaduan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul'       => 'required|string|max:200',
            'deskripsi'   => 'required|string|max:2000',
            'jenis'       => 'required|in:kerusakan,tagihan,pelayanan,lainnya',
            'foto'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'judul.required'     => 'Judul pengaduan wajib diisi.',
            'deskripsi.required' => 'Deskripsi pengaduan wajib diisi.',
            'jenis.required'     => 'Pilih jenis pengaduan.',
            'foto.image'         => 'File harus berupa gambar.',
            'foto.max'           => 'Ukuran foto maksimal 2MB.',
        ]);

        $pelanggan = Auth::user()->pelanggan;

        // Generate nomor pengaduan
        $nomorPengaduan = 'PGD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));

        // Upload foto (opsional)
        $fotoPath = null;
        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            $fotoPath = $request->file('foto')->store('pengaduan', 'public');
        }

        $pengaduan = Pengaduan::create([
            'pelanggan_id'   => $pelanggan->id,
            'nomor_pengaduan'=> $nomorPengaduan,
            'judul'          => $request->judul,
            'deskripsi'      => $request->deskripsi,
            'foto'           => $fotoPath,
            'jenis'          => $request->jenis,
            'status'         => 'baru',
            'prioritas'      => 'sedang',
        ]);

        AktivitasLog::catat('buat_pengaduan', "Pelanggan membuat pengaduan {$nomorPengaduan}", 'Pengaduan', $pengaduan->id);

        return redirect()->route('pelanggan.pengaduan.show', $pengaduan)
            ->with('success', "Pengaduan {$nomorPengaduan} berhasil dikirim. Kami akan segera menindaklanjuti.");
    }

    public function show(Pengaduan $pengaduan)
    {
        $pelanggan = Auth::user()->pelanggan;

        if ($pengaduan->pelanggan_id !== $pelanggan->id) {
            abort(403);
        }

        return view('pelanggan.pengaduan.show', compact('pengaduan'));
    }
}