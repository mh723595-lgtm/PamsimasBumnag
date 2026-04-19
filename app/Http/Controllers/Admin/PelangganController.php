<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\User;
use App\Models\AktivitasLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $query = Pelanggan::with('user')->orderByDesc('created_at');

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) =>
                $q->where('nama_pelanggan', 'like', "%$s%")
                  ->orWhere('nomor_pelanggan', 'like', "%$s%")
                  ->orWhere('no_hp', 'like', "%$s%")
            );
        }

        $pelanggan = $query->paginate(15)->withQueryString();

        $stats = [
            'total'    => Pelanggan::count(),
            'aktif'    => Pelanggan::where('status', 'aktif')->count(),
            'nonaktif' => Pelanggan::where('status', 'nonaktif')->count(),
        ];

        return view('admin.pelanggan.index', compact('pelanggan', 'stats'));
    }

    public function create()
    {
        // Generate nomor pelanggan otomatis
        $last = Pelanggan::orderByDesc('id')->first();
        $nextNo = $last ? (int) substr($last->nomor_pelanggan, 4) + 1 : 1;
        $nomorPelanggan = 'PLG-' . str_pad($nextNo, 4, '0', STR_PAD_LEFT);

        return view('admin.pelanggan.create', compact('nomorPelanggan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:100',
            'email'          => 'required|email|unique:users,email',
            'password'       => 'required|min:6',
            'alamat'         => 'required|string',
            'rt_rw'          => 'nullable|string|max:20',
            'desa'           => 'nullable|string|max:100',
            'kecamatan'      => 'nullable|string|max:100',
            'no_hp'          => 'nullable|string|max:20',
            'no_ktp'         => 'nullable|string|max:20',
            'meteran_awal'   => 'required|integer|min:0',
            'tanggal_daftar' => 'required|date',
            'status'         => 'required|in:aktif,nonaktif,tutup',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name'      => $request->nama_pelanggan,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'role'      => 'pelanggan',
                'is_active' => true,
            ]);

            $last = Pelanggan::orderByDesc('id')->first();
            $nextNo = $last ? (int) substr($last->nomor_pelanggan, 4) + 1 : 1;
            $nomorPelanggan = 'PLG-' . str_pad($nextNo, 4, '0', STR_PAD_LEFT);

            Pelanggan::create([
                'user_id'        => $user->id,
                'nomor_pelanggan'=> $nomorPelanggan,
                'nama_pelanggan' => $request->nama_pelanggan,
                'alamat'         => $request->alamat,
                'rt_rw'          => $request->rt_rw,
                'desa'           => $request->desa,
                'kecamatan'      => $request->kecamatan,
                'no_hp'          => $request->no_hp,
                'no_ktp'         => $request->no_ktp,
                'meteran_awal'   => $request->meteran_awal,
                'tanggal_daftar' => $request->tanggal_daftar,
                'status'         => $request->status,
            ]);
        });

        AktivitasLog::catat('create_pelanggan', "Tambah pelanggan: {$request->nama_pelanggan}");

        return redirect()->route('admin.pelanggan.index')
            ->with('success', "Pelanggan {$request->nama_pelanggan} berhasil ditambahkan.");
    }

    public function show(Pelanggan $pelanggan)
    {
        $pelanggan->load(['user', 'tagihanAir' => fn($q) => $q->orderByDesc('tahun')->orderByDesc('bulan')->take(12)]);
        return view('admin.pelanggan.show', compact('pelanggan'));
    }

    public function edit(Pelanggan $pelanggan)
    {
        return view('admin.pelanggan.edit', compact('pelanggan'));
    }

    public function update(Request $request, Pelanggan $pelanggan)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:100',
            'alamat'         => 'required|string',
            'rt_rw'          => 'nullable|string|max:20',
            'desa'           => 'nullable|string|max:100',
            'kecamatan'      => 'nullable|string|max:100',
            'no_hp'          => 'nullable|string|max:20',
            'no_ktp'         => 'nullable|string|max:20',
            'meteran_awal'   => 'required|integer|min:0',
            'tanggal_daftar' => 'required|date',
            'status'         => 'required|in:aktif,nonaktif,tutup',
        ]);

        $pelanggan->update($request->only([
            'nama_pelanggan','alamat','rt_rw','desa','kecamatan',
            'no_hp','no_ktp','meteran_awal','tanggal_daftar','status'
        ]));

        $pelanggan->user->update(['name' => $request->nama_pelanggan]);

        AktivitasLog::catat('update_pelanggan', "Update pelanggan: {$pelanggan->nomor_pelanggan}", 'Pelanggan', $pelanggan->id);

        return redirect()->route('admin.pelanggan.index')
            ->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function destroy(Pelanggan $pelanggan)
    {
        AktivitasLog::catat('delete_pelanggan', "Hapus pelanggan: {$pelanggan->nomor_pelanggan}", 'Pelanggan', $pelanggan->id);
        $pelanggan->user->delete(); // cascade ke pelanggan juga
        return redirect()->route('admin.pelanggan.index')
            ->with('success', 'Pelanggan berhasil dihapus.');
    }
}