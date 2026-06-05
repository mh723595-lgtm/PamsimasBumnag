<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssignPetugas;
use App\Models\Jorong;
use App\Models\Petugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignPetugasController extends Controller
{
    public function index(Request $request)
    {
        $query = AssignPetugas::with(['petugas', 'jorong'])->orderBy('created_at', 'desc');

        if ($request->filled('jorong')) {
            $query->where('jorong_id', $request->jorong);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('petugas', fn($q) =>
                $q->where('nama_petugas', 'like', "%$s%")
            );
        }

        $assigns    = $query->get();
        $petugas    = Petugas::where('status', 'aktif')->orderBy('nama_petugas')->get();
        $jorongList = Jorong::where('aktif', true)->orderBy('nama_jorong')->get();

        $stats = [
            'total_petugas' => Petugas::where('status', 'aktif')->count(),
            'total_jorong'  => Jorong::where('aktif', true)->count(),
            'sudah_assign'  => AssignPetugas::where('aktif', true)->distinct('petugas_id')->count('petugas_id'),
            'belum_assign'  => Petugas::where('status', 'aktif')
                ->whereNotIn('id', AssignPetugas::where('aktif', true)->pluck('petugas_id'))
                ->count(),
        ];

        return view('admin.assign-petugas.index', compact('assigns', 'petugas', 'jorongList', 'stats'));
    }

    public function store(Request $request)
    {
        
        $request->validate([
            'petugas_id' => 'required|exists:petugas,id',
            'jorong_id'  => 'required|exists:jorong,id',
            'periode'    => 'nullable|string|max:50',
            'catatan'    => 'nullable|string|max:255',
        ]);

        $jorong = Jorong::findOrFail($request->jorong_id);

        $existing = AssignPetugas::where('petugas_id', $request->petugas_id)
            ->where('jorong_id', $request->jorong_id)
            ->first();

        if ($existing) {
            $existing->update([
                'aktif'   => true,
                'periode' => $request->periode ?? 'permanen',
            ]);
            return response()->json(['success' => true, 'message' => 'Assign berhasil diaktifkan kembali!']);
        }

        AssignPetugas::create([
            'petugas_id'  => $request->petugas_id,
            'jorong_id'   => $request->jorong_id,
            'periode'     => $request->periode ?? 'permanen',
            'catatan'     => $request->catatan,
            'aktif'       => true,
            'dibuat_oleh' => Auth::id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Petugas berhasil diassign ke ' . $jorong->nama_jorong . '!']);
    }

    public function destroy(AssignPetugas $assignPetugas)
    {
        $assignPetugas->delete();
        return response()->json(['success' => true, 'message' => 'Assign berhasil dihapus!']);
    }

    public function toggleAktif(AssignPetugas $assignPetugas)
    {
        $assignPetugas->update(['aktif' => !$assignPetugas->aktif]);
        return response()->json([
            'success' => true,
            'aktif'   => $assignPetugas->aktif,
            'message' => 'Status assign berhasil diubah!'
        ]);
    }

    public function update(Request $request, AssignPetugas $assignPetugas)
{
    $request->validate([
        'jorong_id' => 'required|exists:jorong,id',
        'periode'   => 'nullable|string|max:50',
        'catatan'   => 'nullable|string|max:255',
    ]);

    $jorong = Jorong::findOrFail($request->jorong_id);

    $assignPetugas->update([
        'jorong_id' => $request->jorong_id,
        'periode'   => $request->periode ?? 'permanen',
        'catatan'   => $request->catatan,
    ]);

    return response()->json(['success' => true, 'message' => 'Assign berhasil diperbarui ke ' . $jorong->nama_jorong . '!']);
}
public function detailPetugas(Petugas $petugas)
{
    $assigns = AssignPetugas::with('jorong')
        ->where('petugas_id', $petugas->id)
        ->where('aktif', true)
        ->get();

    $jorongIds = $assigns->pluck('jorong_id');

    $pelanggan = \App\Models\Pelanggan::with('jorong')
        ->whereIn('jorong_id', $jorongIds)
        ->where('status', 'aktif')
        ->orderBy('jorong_id')
        ->orderBy('nomor_pelanggan')
        ->get();

    return response()->json([
        'petugas'     => [
            'nama'    => $petugas->nama_petugas,
            'jabatan' => $petugas->jabatan ?? '-',
        ],
        'jorong_list' => $assigns->map(fn($a) => $a->jorong->nama_jorong ?? '-'),
        'pelanggan'   => $pelanggan->map(fn($p) => [
            'nomor'  => $p->nomor_pelanggan,
            'nama'   => $p->nama_pelanggan,
            'alamat' => $p->alamat ?? '-',
            'jorong' => $p->jorong->nama_jorong ?? '-',
            'no_hp'  => $p->no_hp ?? '-',
        ]),
        'total' => $pelanggan->count(),
    ]);
}


}