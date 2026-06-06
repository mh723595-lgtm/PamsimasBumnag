<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Petugas;
use App\Models\User;
use App\Models\AktivitasLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class PetugasController extends Controller
{
    public function index(Request $request)
    {
        $query = Petugas::with('user')->orderByDesc('created_at');
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(
                fn($q) =>
                $q->where('nama_petugas', 'like', "%$s%")
                    ->orWhere('nip', 'like', "%$s%")
                    ->orWhere('nik', 'like', "%$s%")
            );
        }
        $petugas = $query->paginate(15)->withQueryString();
        return view('admin.petugas.index', compact('petugas'));
    }

    public function create()
    {
        $jabatanList = $this->jabatanList();
        return view('admin.petugas.create', compact('jabatanList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_petugas'  => 'required|string|max:100',
            'email'         => 'required|email|unique:users,email',
            'password' => [
                'required',
                'min:8',
                Password::min(8)
                    ->letters()
                    ->numbers()
                    ->symbols(),
            ],
            'nip'           => 'nullable|string|unique:petugas,nip',
            'nik'           => 'nullable|digits:16|unique:petugas,nik',
            'jabatan'       => 'nullable|string|max:100',
            'no_hp'         => 'nullable|digits_between:10,15',
            'alamat'        => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'tmt'           => 'nullable|date',
            'foto'          => 'nullable|image|max:2048',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name'      => $request->nama_petugas,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'role'      => 'petugas',
                'is_active' => true,
            ]);

            $foto = null;
            if ($request->hasFile('foto')) {
                $foto = $request->file('foto')->store('petugas/foto', 'public');
            }

            Petugas::create([
                'user_id'       => $user->id,
                'nip'           => $request->nip,
                'nik'           => $request->nik,
                'nama_petugas'  => $request->nama_petugas,
                'jabatan'       => $request->jabatan,
                'no_hp'         => $request->no_hp,
                'alamat'        => $request->alamat,
                'tanggal_lahir' => $request->tanggal_lahir,
                'tmt'           => $request->tmt,
                'foto'          => $foto,
                'status'        => 'aktif',
            ]);
        });

        AktivitasLog::catat('create_petugas', "Tambah petugas: {$request->nama_petugas}");
        return redirect()->route('admin.petugas.index')->with('success', 'Petugas berhasil ditambahkan.');
    }

    public function edit(Petugas $petugas)
    {
        $jabatanList = $this->jabatanList();
        return view('admin.petugas.edit', compact('petugas', 'jabatanList'));
    }

    public function update(Request $request, Petugas $petugas)
    {
        $request->validate([
            'nama_petugas'  => 'required|string|max:100',
            'nip'           => ['nullable', 'string', Rule::unique('petugas', 'nip')->ignore($petugas->id)],
            'nik'           => ['nullable', 'digits:16', Rule::unique('petugas', 'nik')->ignore($petugas->id)],
            'password.regex' => 'Password harus mengandung huruf, angka, dan karakter spesial.',
            'jabatan'       => 'nullable|string|max:100',
            'no_hp'         => 'nullable|string|max:20',
            'alamat'        => 'nullable|string',
            'status'        => 'required|in:aktif,nonaktif',
            'tanggal_lahir' => 'nullable|date',
            'tmt'           => 'nullable|date',
            'foto'          => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['nama_petugas', 'nip', 'nik', 'jabatan', 'no_hp', 'alamat', 'status', 'tanggal_lahir', 'tmt']);

        if ($request->hasFile('foto')) {
            if ($petugas->foto) Storage::disk('public')->delete($petugas->foto);
            $data['foto'] = $request->file('foto')->store('petugas/foto', 'public');
        }

        $petugas->update($data);
        $petugas->user->update(['name' => $request->nama_petugas]);

        AktivitasLog::catat('update_petugas', "Update petugas: {$petugas->nama_petugas}", 'Petugas', $petugas->id);
        return redirect()->route('admin.petugas.index')->with('success', 'Data petugas berhasil diperbarui.');
    }

    public function destroy(Petugas $petugas)
    {
        if ($petugas->foto) Storage::disk('public')->delete($petugas->foto);
        AktivitasLog::catat('delete_petugas', "Hapus petugas: {$petugas->nama_petugas}", 'Petugas', $petugas->id);
        $petugas->user->delete();
        return redirect()->route('admin.petugas.index')->with('success', 'Petugas berhasil dihapus.');
    }

    private function jabatanList(): array
    {
        return [
            'Koordinator Lapangan',
            'Teknisi Lapangan',
            'Petugas Meteran',
            'Petugas Administrasi',
            'Petugas Keuangan',
            'Operator Pompa',
            'Petugas Kebersihan Jaringan',
            'Supervisor',
        ];
    }
}
