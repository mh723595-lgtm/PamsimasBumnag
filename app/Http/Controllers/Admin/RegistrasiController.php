<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pelanggan;
use App\Models\Petugas;
use App\Models\Notifikasi;
use App\Models\AktivitasLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegistrasiController extends Controller
{
    /**
     * Daftar semua pendaftar yang menunggu persetujuan
     */
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'semua');

        $pelangganPending = Pelanggan::with('user')
            ->where('status_registrasi', 'pending')
            ->orderByDesc('created_at')
            ->get();

        $petugasPending = Petugas::with('user')
            ->where('status_registrasi', 'pending')
            ->orderByDesc('created_at')
            ->get();

        // Riwayat persetujuan (7 hari terakhir)
        $riwayat = collect();
        $pelangganRiwayat = Pelanggan::with('user')
            ->whereIn('status_registrasi', ['approved', 'rejected'])
            ->where('updated_at', '>=', now()->subDays(7))
            ->orderByDesc('updated_at')
            ->get()
            ->map(fn($p) => (object)['type' => 'pelanggan', 'data' => $p]);

        $petugasRiwayat = Petugas::with('user')
            ->whereIn('status_registrasi', ['approved', 'rejected'])
            ->where('updated_at', '>=', now()->subDays(7))
            ->orderByDesc('updated_at')
            ->get()
            ->map(fn($p) => (object)['type' => 'petugas', 'data' => $p]);

        $riwayat = $pelangganRiwayat->merge($petugasRiwayat)
            ->sortByDesc(fn($item) => $item->data->updated_at)
            ->values();

        $totalPending = $pelangganPending->count() + $petugasPending->count();

        return view('admin.registrasi.index', compact(
            'pelangganPending', 'petugasPending', 'riwayat', 'totalPending', 'tab'
        ));
    }

    /**
     * Detail pendaftar
     */
    public function show(Request $request, string $type, int $id)
    {
        if ($type === 'pelanggan') {
            $data = Pelanggan::with('user')->findOrFail($id);
        } else {
            $data = Petugas::with('user')->findOrFail($id);
        }
        return view('admin.registrasi.show', compact('data', 'type'));
    }

    /**
     * Approve pendaftaran
     */
    public function approve(Request $request, string $type, int $id)
    {
        $request->validate([
            'catatan' => 'nullable|string|max:500',
            'meteran_awal' => 'nullable|integer|min:0', // untuk pelanggan
        ]);

        if ($type === 'pelanggan') {
            $data = Pelanggan::with('user')->findOrFail($id);

            $data->update([
                'status'              => 'aktif',
                'status_registrasi'   => 'approved',
                'catatan_registrasi'  => $request->catatan,
                'approved_at'         => now(),
                'approved_by'         => Auth::id(),
                'meteran_awal'        => $request->meteran_awal ?? 0,
            ]);

            // Aktifkan user
            $data->user->update(['is_active' => true]);

            // Notifikasi ke pelanggan
            Notifikasi::kirim(
                $data->user_id,
                '✅ Pendaftaran Disetujui',
                "Selamat! Pendaftaran Anda sebagai pelanggan PAMSIMAS telah disetujui. " .
                "No. Pelanggan: {$data->nomor_pelanggan}. Silakan login untuk mengakses sistem." .
                ($request->catatan ? " Catatan admin: {$request->catatan}" : ''),
                'success'
            );

            $nama = $data->nama_pelanggan;

        } else {
            $data = Petugas::with('user')->findOrFail($id);

            $data->update([
                'status'              => 'aktif',
                'status_registrasi'   => 'approved',
                'catatan_registrasi'  => $request->catatan,
                'approved_at'         => now(),
                'approved_by'         => Auth::id(),
            ]);

            $data->user->update(['is_active' => true]);

            Notifikasi::kirim(
                $data->user_id,
                '✅ Pendaftaran Petugas Disetujui',
                "Pendaftaran Anda sebagai petugas PAMSIMAS telah disetujui. " .
                "Silakan login untuk mengakses sistem." .
                ($request->catatan ? " Catatan: {$request->catatan}" : ''),
                'success'
            );

            $nama = $data->nama_petugas;
        }

        AktivitasLog::catat('approve_registrasi', "Menyetujui pendaftaran {$type}: {$nama}");

        return redirect()->route('admin.registrasi.index')
            ->with('success', "Pendaftaran {$nama} berhasil disetujui. Akun telah diaktifkan.");
    }

    /**
     * Reject pendaftaran
     */
    public function reject(Request $request, string $type, int $id)
    {
        $request->validate([
            'catatan' => 'required|string|max:500',
        ], [
            'catatan.required' => 'Alasan penolakan wajib diisi.',
        ]);

        if ($type === 'pelanggan') {
            $data = Pelanggan::with('user')->findOrFail($id);
            $data->update([
                'status_registrasi'  => 'rejected',
                'catatan_registrasi' => $request->catatan,
                'approved_by'        => Auth::id(),
                'approved_at'        => now(),
            ]);

            Notifikasi::kirim(
                $data->user_id,
                '❌ Pendaftaran Ditolak',
                "Mohon maaf, pendaftaran Anda sebagai pelanggan PAMSIMAS ditolak. " .
                "Alasan: {$request->catatan}. Hubungi kantor PAMSIMAS untuk informasi lebih lanjut.",
                'error'
            );
            $nama = $data->nama_pelanggan;

        } else {
            $data = Petugas::with('user')->findOrFail($id);
            $data->update([
                'status_registrasi'  => 'rejected',
                'catatan_registrasi' => $request->catatan,
                'approved_by'        => Auth::id(),
                'approved_at'        => now(),
            ]);

            Notifikasi::kirim(
                $data->user_id,
                '❌ Pendaftaran Petugas Ditolak',
                "Pendaftaran Anda sebagai petugas ditolak. Alasan: {$request->catatan}.",
                'error'
            );
            $nama = $data->nama_petugas;
        }

        AktivitasLog::catat('reject_registrasi', "Menolak pendaftaran {$type}: {$nama}");

        return redirect()->route('admin.registrasi.index')
            ->with('success', "Pendaftaran {$nama} telah ditolak.");
    }
}
