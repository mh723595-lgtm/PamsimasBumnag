<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AktivitasLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 6 karakter.',
        ]);

        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            return back()
                ->withErrors(['email' => 'Email atau password salah.'])
                ->withInput($request->only('email'));
        }

        $request->session()->regenerate();
        $user = Auth::user();

        // Cek akun nonaktif karena menunggu persetujuan
        if (! $user->is_active) {
            // Cek apakah nonaktif karena registrasi pending
            $statusRegistrasi = null;

            if ($user->role === 'pelanggan' && $user->pelanggan) {
                $statusRegistrasi = $user->pelanggan->status_registrasi ?? null;
            } elseif ($user->role === 'petugas' && $user->petugas) {
                $statusRegistrasi = $user->petugas->status_registrasi ?? null;
            }

            Auth::logout();

            $pesan = match($statusRegistrasi) {
                'pending'  => '⏳ Akun Anda sedang menunggu persetujuan admin. Harap tunggu notifikasi email atau hubungi kantor PAMSIMAS.',
                'rejected' => '❌ Pendaftaran Anda ditolak. Hubungi admin PAMSIMAS untuk informasi lebih lanjut.',
                default    => '🚫 Akun Anda tidak aktif. Hubungi administrator untuk mengaktifkan akun.',
            };

            return back()->withErrors(['email' => $pesan])->withInput($request->only('email'));
        }

       // AktivitasLog::catat('login', 'User berhasil login ke sistem');

        return $this->redirectByRole($user->role);
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            AktivitasLog::catat('logout', 'User logout dari sistem');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Berhasil logout. Sampai jumpa!');
    }

    private function redirectByRole(string $role): \Illuminate\Http\RedirectResponse
    {
        return match($role) {
            'admin'     => redirect()->route('admin.dashboard'),
            'petugas'   => redirect()->route('petugas.dashboard'),
            'pelanggan' => redirect()->route('pelanggan.dashboard'),
            default     => redirect('/'),
        };
    }
}
