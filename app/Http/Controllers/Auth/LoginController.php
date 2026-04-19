<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Akun Anda tidak aktif. Hubungi administrator.']);
            }

            // Log aktivitas login
            \App\Models\AktivitasLog::catat('login', 'User login ke sistem');

            return $this->redirectByRole($user->role);
        }

        return back()
            ->withErrors(['email' => 'Email atau password salah.'])
            ->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        \App\Models\AktivitasLog::catat('logout', 'User logout dari sistem');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Berhasil logout.');
    }

    private function redirectByRole(string $role)
    {
        return match($role) {
            'admin'     => redirect()->route('admin.dashboard'),
            'petugas'   => redirect()->route('petugas.dashboard'),
            'pelanggan' => redirect()->route('pelanggan.dashboard'),
            default     => redirect('/'),
        };
    }
}