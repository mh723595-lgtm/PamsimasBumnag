<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Usage: middleware('role:admin') / middleware('role:petugas') / middleware('role:pelanggan')
     */
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Akun Anda tidak aktif.');
        }

        if (!in_array($user->role, $roles)) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}
