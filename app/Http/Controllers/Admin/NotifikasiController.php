<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    /**
     * Daftar semua notifikasi milik user yang login
     */
    public function index(Request $request)
    {
        $query = Notifikasi::where('user_id', Auth::id())
            ->orderByDesc('created_at');

        // Filter belum dibaca
        if ($request->boolean('belum_dibaca')) {
            $query->where('sudah_dibaca', false);
        }

        // Filter tipe
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        $notifikasi   = $query->paginate(20)->withQueryString();
        $jumlahBelum  = Notifikasi::where('user_id', Auth::id())
                            ->where('sudah_dibaca', false)->count();

        return view('admin.notifikasi.index', compact('notifikasi', 'jumlahBelum'));
    }

    /**
     * Tandai satu notifikasi sudah dibaca
     */
    public function baca(Notifikasi $notifikasi)
    {
        // Guard: hanya boleh akses notif milik sendiri
        if ($notifikasi->user_id !== Auth::id()) {
            abort(403);
        }

        $notifikasi->update(['sudah_dibaca' => true]);

        // Redirect ke URL notifikasi jika ada
        if ($notifikasi->url) {
            return redirect($notifikasi->url);
        }

        return back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    /**
     * Tandai semua notifikasi milik user sudah dibaca
     */
    public function bacaSemua()
    {
        Notifikasi::where('user_id', Auth::id())
            ->where('sudah_dibaca', false)
            ->update(['sudah_dibaca' => true]);

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }

    /**
     * Hapus notifikasi yang sudah dibaca (housekeeping)
     */
    public function hapusDibaca()
    {
        $jumlah = Notifikasi::where('user_id', Auth::id())
            ->where('sudah_dibaca', true)
            ->delete();

        return back()->with('success', "{$jumlah} notifikasi sudah dibaca berhasil dihapus.");
    }

    /**
     * Ambil jumlah notifikasi belum dibaca (untuk AJAX/polling)
     */
    public function jumlah()
    {
        $jumlah = Notifikasi::where('user_id', Auth::id())
            ->where('sudah_dibaca', false)
            ->count();

        return response()->json(['jumlah' => $jumlah]);
    }
}
