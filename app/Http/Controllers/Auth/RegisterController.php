<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pelanggan;
use App\Models\Petugas;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    /**
     * Form registrasi
     */
    public function showForm(string $role = 'pelanggan')
    {
        if (!in_array($role, ['pelanggan', 'petugas'])) {
            abort(404);
        }
        return view('auth.register', compact('role'));
    }

    /**
     * Proses registrasi
     */
    public function register(Request $request)
    {
        $role = $request->input('role', 'pelanggan');

        $rules = [
            'role'          => 'required|in:pelanggan,petugas',
            'nama'          => 'required|string|max:150',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|min:6|confirmed',
            'no_hp'         => 'nullable|string|max:20',
            'alamat'        => 'required|string|max:500',
        ];

        if ($role === 'pelanggan') {
            $rules['desa']       = 'nullable|string|max:100';
            $rules['kecamatan']  = 'nullable|string|max:100';
            $rules['no_ktp']     = 'nullable|string|max:20';
        }

        if ($role === 'petugas') {
            $rules['jabatan'] = 'nullable|string|max:100';
            $rules['nip']     = 'nullable|string|max:30|unique:petugas,nip';
        }

        $request->validate($rules, [
            'nama.required'     => 'Nama lengkap wajib diisi.',
            'email.required'    => 'Email wajib diisi.',
            'email.unique'      => 'Email sudah terdaftar, gunakan email lain.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 6 karakter.',
            'password.confirmed'=> 'Konfirmasi password tidak cocok.',
            'alamat.required'   => 'Alamat wajib diisi.',
        ]);

        try {
            DB::transaction(function () use ($request, $role) {
                // Buat user (nonaktif dulu, menunggu persetujuan admin)
                $user = User::create([
                    'name'      => $request->nama,
                    'email'     => $request->email,
                    'password'  => Hash::make($request->password),
                    'role'      => $role,
                    'is_active' => false, // Nonaktif sampai disetujui admin
                ]);

                if ($role === 'pelanggan') {
                    // Generate nomor pelanggan sementara
                    $lastPelanggan = Pelanggan::orderByDesc('id')->first();
                    $nextNo = $lastPelanggan ? ((int) substr($lastPelanggan->nomor_pelanggan, 4) + 1) : 1;
                    $nomorPelanggan = 'PLG-' . str_pad($nextNo, 4, '0', STR_PAD_LEFT);

                    Pelanggan::create([
                        'user_id'             => $user->id,
                        'nomor_pelanggan'     => $nomorPelanggan,
                        'nama_pelanggan'      => $request->nama,
                        'alamat'              => $request->alamat,
                        'desa'                => $request->desa,
                        'kecamatan'           => $request->kecamatan,
                        'no_hp'               => $request->no_hp,
                        'no_ktp'              => $request->no_ktp,
                        'meteran_awal'        => 0,
                        'status'              => 'nonaktif',
                        'status_registrasi'   => 'pending',
                        'tanggal_daftar'      => now()->toDateString(),
                    ]);
                } else {
                    Petugas::create([
                        'user_id'           => $user->id,
                        'nip'               => $request->nip,
                        'nama_petugas'      => $request->nama,
                        'jabatan'           => $request->jabatan,
                        'no_hp'             => $request->no_hp,
                        'alamat'            => $request->alamat,
                        'status'            => 'nonaktif',
                        'status_registrasi' => 'pending',
                    ]);
                }

                // Kirim notifikasi ke semua admin
                $admins = User::where('role', 'admin')->where('is_active', true)->get();
                foreach ($admins as $admin) {
                    Notifikasi::kirim(
                        $admin->id,
                        '🆕 Pendaftaran Akun Baru',
                        "Ada pendaftaran akun " . ucfirst($role) . " baru: {$request->nama} ({$request->email}). Harap segera tinjau dan setujui.",
                        'info'
                    );
                }
            });

            return redirect()->route('login')
                ->with('success', '✅ Pendaftaran berhasil! Akun Anda sedang menunggu persetujuan admin. Anda akan dapat login setelah disetujui.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan saat mendaftar. Coba lagi.');
        }
    }
}
