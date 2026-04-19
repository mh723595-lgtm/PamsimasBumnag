<?php
// app/Http/Controllers/Admin/PetugasController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Petugas;
use App\Models\User;
use App\Models\AktivitasLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PetugasController extends Controller
{
    public function index(Request $request)
    {
        $query = Petugas::with('user')->orderByDesc('created_at');
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) =>
                $q->where('nama_petugas','like',"%$s%")
                  ->orWhere('nip','like',"%$s%")
            );
        }
        $petugas = $query->paginate(15)->withQueryString();
        return view('admin.petugas.index', compact('petugas'));
    }

    public function create()
    {
        return view('admin.petugas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_petugas' => 'required|string|max:100',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|min:6',
            'nip'          => 'nullable|string|unique:petugas,nip',
            'jabatan'      => 'nullable|string|max:100',
            'no_hp'        => 'nullable|string|max:20',
            'alamat'       => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name'      => $request->nama_petugas,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'role'      => 'petugas',
                'is_active' => true,
            ]);
            Petugas::create([
                'user_id'      => $user->id,
                'nip'          => $request->nip,
                'nama_petugas' => $request->nama_petugas,
                'jabatan'      => $request->jabatan,
                'no_hp'        => $request->no_hp,
                'alamat'       => $request->alamat,
                'status'       => 'aktif',
            ]);
        });

        AktivitasLog::catat('create_petugas', "Tambah petugas: {$request->nama_petugas}");
        return redirect()->route('admin.petugas.index')->with('success', 'Petugas berhasil ditambahkan.');
    }

    public function edit(Petugas $petugas)
    {
        return view('admin.petugas.edit', compact('petugas'));
    }

    public function update(Request $request, Petugas $petugas)
    {
        $request->validate([
            'nama_petugas' => 'required|string|max:100',
            'nip'          => ['nullable','string', Rule::unique('petugas','nip')->ignore($petugas->id)],
            'jabatan'      => 'nullable|string|max:100',
            'no_hp'        => 'nullable|string|max:20',
            'alamat'       => 'nullable|string',
            'status'       => 'required|in:aktif,nonaktif',
        ]);

        $petugas->update($request->only(['nama_petugas','nip','jabatan','no_hp','alamat','status']));
        $petugas->user->update(['name' => $request->nama_petugas]);

        AktivitasLog::catat('update_petugas', "Update petugas: {$petugas->nama_petugas}", 'Petugas', $petugas->id);
        return redirect()->route('admin.petugas.index')->with('success', 'Data petugas berhasil diperbarui.');
    }

    public function destroy(Petugas $petugas)
    {
        AktivitasLog::catat('delete_petugas', "Hapus petugas: {$petugas->nama_petugas}", 'Petugas', $petugas->id);
        $petugas->user->delete();
        return redirect()->route('admin.petugas.index')->with('success', 'Petugas berhasil dihapus.');
    }
}