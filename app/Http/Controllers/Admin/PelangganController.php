<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\Petugas;
use App\Models\Jorong;
use App\Models\User;
use App\Models\AktivitasLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $query = Pelanggan::with(['user', 'jorong', 'petugas'])->orderByDesc('created_at');

        if ($request->filled('status'))   $query->where('status', $request->status);
        if ($request->filled('jorong_id')) $query->where('jorong_id', $request->jorong_id);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) =>
                $q->where('nama_pelanggan', 'like', "%$s%")
                  ->orWhere('nomor_pelanggan', 'like', "%$s%")
                  ->orWhere('no_hp', 'like', "%$s%")
            );
        }

        $pelanggan   = $query->paginate(10)->withQueryString();
        $jorongList  = Jorong::where('aktif', true)->orderBy('nama_jorong')->get();
        $petugasList = Petugas::orderBy('nama_petugas')->get();

        $stats = [
            'total'    => Pelanggan::count(),
            'aktif'    => Pelanggan::where('status', 'aktif')->count(),
            'nonaktif' => Pelanggan::where('status', 'nonaktif')->count(),
        ];

        return view('admin.pelanggan.index', compact('pelanggan', 'stats', 'jorongList', 'petugasList'));
    }

    public function create()
    {
        $last = Pelanggan::orderByDesc('id')->first();
        $nextNo = $last ? (int) substr($last->nomor_pelanggan, 4) + 1 : 1;
        $nomorPelanggan = 'PLG-' . str_pad($nextNo, 4, '0', STR_PAD_LEFT);
        $jorongList = Jorong::where('aktif', true)->orderBy('nama_jorong')->get();

        return view('admin.pelanggan.create', compact('nomorPelanggan', 'jorongList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pelanggan'           => 'required|string|max:150',
            'email'                    => 'required|email|unique:users,email',
            'password'                 => 'required|min:6',
            'alamat'                   => 'required|string',
            'rt_rw'                    => 'nullable|string|max:20',
            'provinsi'                 => 'nullable|string|max:100',
            'kabupaten'                => 'nullable|string|max:100',
            'kecamatan'                => 'nullable|string|max:100',
            'desa'                     => 'nullable|string|max:100',
            'no_hp'                    => 'nullable|digits_between:10,15',
            'no_ktp'                   => 'nullable|digits:16',
            'meteran_awal'             => 'required|integer|min:0',
            'nomor_meteran'            => 'nullable|string|max:50|unique:pelanggan,nomor_meteran',
            'nomor_pelanggan_external' => 'nullable|string|max:50',
            'latitude'                 => 'nullable|numeric|between:-90,90',
            'longitude'                => 'nullable|numeric|between:-180,180',
            'tanggal_daftar'           => 'required|date',
            'status'                   => 'required|in:aktif,nonaktif,tutup',
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
                'user_id'                  => $user->id,
                'nomor_pelanggan'          => $nomorPelanggan,
                'nama_pelanggan'           => $request->nama_pelanggan,
                'alamat'                   => $request->alamat,
                'rt_rw'                    => $request->rt_rw,
                'provinsi'                 => $request->provinsi,
                'kabupaten'                => $request->kabupaten,
                'kecamatan'                => $request->kecamatan,
                'desa'                     => $request->desa,
                'no_hp'                    => $request->no_hp,
                'no_ktp'                   => $request->no_ktp,
                'meteran_awal'             => $request->meteran_awal,
                'nomor_meteran'            => $request->nomor_meteran,
                'nomor_pelanggan_external' => $request->nomor_pelanggan_external,
                'latitude'                 => $request->latitude ?: null,
                'longitude'                => $request->longitude ?: null,
                'tanggal_daftar'           => $request->tanggal_daftar,
                'status'                   => $request->status,
                'status_registrasi'        => 'approved',
                'approved_at'              => now(),
                'approved_by'              => Auth::id(),
            ]);
        });

        AktivitasLog::catat('create_pelanggan', "Tambah pelanggan: {$request->nama_pelanggan}");

        return redirect()->route('admin.pelanggan.index')
            ->with('success', "Pelanggan {$request->nama_pelanggan} berhasil ditambahkan.");
    }

    public function show(Request $request, Pelanggan $pelanggan)
    {
        $pelanggan->load(['user', 'petugas', 'jorong']);

        $tagihanAir = $pelanggan->tagihanAir()
            ->orderByDesc('tahun')->orderByDesc('bulan')
            ->paginate(10, ['*'], 'tagihan_page')
            ->withQueryString();

        $meteranAir = $pelanggan->meteranAir()
            ->with(['petugas'])
            ->orderByDesc('tahun')->orderByDesc('bulan')
            ->paginate(10, ['*'], 'meteran_page')
            ->withQueryString();

        $petugasList = Petugas::orderBy('nama_petugas')->get();

        return view('admin.pelanggan.show', compact('pelanggan', 'tagihanAir', 'meteranAir', 'petugasList'));
    }

    public function edit(Pelanggan $pelanggan)
    {
        $adaPembayaran = \App\Models\Pembayaran::where('pelanggan_id', $pelanggan->id)->exists();
        $jorongList = Jorong::where('aktif', true)->orderBy('nama_jorong')->get();
        return view('admin.pelanggan.edit', compact('pelanggan', 'adaPembayaran', 'jorongList'));
    }

    public function update(Request $request, Pelanggan $pelanggan)
    {
        $adaPembayaran = \App\Models\Pembayaran::where('pelanggan_id', $pelanggan->id)->exists();

        $request->validate([
            'nama_pelanggan'           => 'required|string|max:150',
            'alamat'                   => 'required|string',
            'rt_rw'                    => 'nullable|string|max:20',
            'provinsi'                 => 'nullable|string|max:100',
            'kabupaten'                => 'nullable|string|max:100',
            'kecamatan'                => 'nullable|string|max:100',
            'desa'                     => 'nullable|string|max:100',
            'no_hp'                    => 'nullable|digits_between:10,15',
            'no_ktp'                   => 'nullable|digits:16',
            'meteran_awal'             => 'required|integer|min:0',
            'nomor_meteran'            => 'nullable|string|max:50|unique:pelanggan,nomor_meteran,' . $pelanggan->id,
            'nomor_pelanggan_external' => 'nullable|string|max:50',
            'latitude'                 => 'nullable|numeric|between:-90,90',
            'longitude'                => 'nullable|numeric|between:-180,180',
            'tanggal_daftar'           => 'required|date',
            'status'                   => 'required|in:aktif,nonaktif,tutup',
        ]);

        $dataUpdate = [
            'nama_pelanggan'           => $request->nama_pelanggan,
            'alamat'                   => $request->alamat,
            'rt_rw'                    => $request->rt_rw,
            'provinsi'                 => $request->provinsi,
            'kabupaten'                => $request->kabupaten,
            'kecamatan'                => $request->kecamatan,
            'desa'                     => $request->desa,
            'no_hp'                    => $request->no_hp,
            'no_ktp'                   => $request->no_ktp,
            'nomor_meteran'            => $request->nomor_meteran,
            'nomor_pelanggan_external' => $request->nomor_pelanggan_external,
            'latitude'                 => $request->latitude ?: null,
            'longitude'                => $request->longitude ?: null,
            'tanggal_daftar'           => $request->tanggal_daftar,
            'jorong_id'                => $request->jorong_id,
            'status'                   => $request->status,
        ];

        if (!$adaPembayaran) {
            $dataUpdate['meteran_awal'] = $request->meteran_awal;
        }

        $pelanggan->update($dataUpdate);
        $pelanggan->user->update(['name' => $request->nama_pelanggan]);

        AktivitasLog::catat('update_pelanggan', "Update pelanggan: {$pelanggan->nomor_pelanggan}", 'Pelanggan', $pelanggan->id);

        return redirect()->route('admin.pelanggan.index')
            ->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function destroy(Pelanggan $pelanggan)
    {
        AktivitasLog::catat('delete_pelanggan', "Hapus pelanggan: {$pelanggan->nomor_pelanggan}", 'Pelanggan', $pelanggan->id);
        $pelanggan->user->delete();
        return redirect()->route('admin.pelanggan.index')
            ->with('success', 'Pelanggan berhasil dihapus.');
    }

    // ── Assign Petugas (single) ────────────────────────────────────────────────
    public function assignPetugas(Request $request, Pelanggan $pelanggan)
    {
        $request->validate(['petugas_id' => 'nullable|exists:petugas,id']);
        $pelanggan->update(['petugas_id' => $request->petugas_id ?: null]);

        $nama = $request->petugas_id
            ? optional(Petugas::find($request->petugas_id))->nama_petugas
            : '(dihapus)';

        AktivitasLog::catat('assign_petugas', "Assign petugas {$nama} ke pelanggan {$pelanggan->nomor_pelanggan}", 'Pelanggan', $pelanggan->id);

        return back()->with('success', "Petugas berhasil di-assign ke {$pelanggan->nama_pelanggan}.");
    }

    // ── Hapus Petugas (single) ─────────────────────────────────────────────────
    public function hapusPetugas(Pelanggan $pelanggan)
    {
        $pelanggan->update(['petugas_id' => null]);
        AktivitasLog::catat('hapus_petugas', "Hapus petugas dari pelanggan {$pelanggan->nomor_pelanggan}", 'Pelanggan', $pelanggan->id);
        return back()->with('success', "Petugas berhasil dihapus dari {$pelanggan->nama_pelanggan}.");
    }

    // ── Bulk Assign Petugas ────────────────────────────────────────────────────
    public function bulkAssignPetugas(Request $request)
    {
        $request->validate([
            'pelanggan_ids'   => 'required|array|min:1',
            'pelanggan_ids.*' => 'exists:pelanggan,id',
            'petugas_id'      => 'nullable|exists:petugas,id',
        ]);

        Pelanggan::whereIn('id', $request->pelanggan_ids)
            ->update(['petugas_id' => $request->petugas_id ?: null]);

        $jumlah = count($request->pelanggan_ids);
        $nama   = $request->petugas_id
            ? optional(Petugas::find($request->petugas_id))->nama_petugas
            : '(dihapus)';

        AktivitasLog::catat('bulk_assign_petugas', "Bulk assign petugas {$nama} ke {$jumlah} pelanggan");

        return back()->with('success', "Petugas {$nama} berhasil di-assign ke {$jumlah} pelanggan.");
    }
}