<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssignPetugas;
use App\Models\Jorong;
use App\Models\Pelanggan;
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
            $query->whereHas(
                'petugas',
                fn($q) =>
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

    /**
     * Detail petugas beserta daftar pelanggan dengan pagination (10 per halaman).
     */
    public function detailPetugas(Request $request, Petugas $petugas)
    {
        $assigns = AssignPetugas::with('jorong')
            ->where('petugas_id', $petugas->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $jorongIds = $assigns->where('aktif', true)->pluck('jorong_id');

        $perPage = 10;
        $page    = max(1, (int) $request->get('page', 1));

        // Query pelanggan (hindari duplikat via groupBy id)
        $pelangganQuery = Pelanggan::with('jorong')
            ->where(function ($q) use ($jorongIds, $petugas) {
                $q->whereIn('jorong_id', $jorongIds)
                    ->orWhere('petugas_id', $petugas->id);
            })
            ->where('status', 'aktif')
            ->orderBy('jorong_id')
            ->orderBy('nomor_pelanggan');

        $total      = $pelangganQuery->distinct()->count('pelanggan.id');
        $pelanggan  = $pelangganQuery->distinct()
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        $lastPage = (int) ceil($total / $perPage);

        return response()->json([
            'petugas' => [
                'nama'    => $petugas->nama_petugas,
                'jabatan' => $petugas->jabatan ?? '-',
                'no_hp'   => $petugas->no_hp   ?? '-',
            ],
            'assigns' => $assigns->map(fn($a) => [
                'id'     => $a->id,
                'jorong' => $a->jorong->nama_jorong ?? '-',
                'periode' => $a->periode,
                'aktif'  => (bool) $a->aktif,
            ]),
            'jorong_list' => $assigns->where('aktif', true)->map(fn($a) => $a->jorong->nama_jorong ?? '-')->values(),
            'pelanggan'   => $pelanggan->map(fn($p) => [
                'id'     => $p->id,
                'nomor'  => $p->nomor_pelanggan,
                'nama'   => $p->nama_pelanggan,
                'alamat' => $p->alamat ?? '-',
                'jorong' => $p->jorong->nama_jorong ?? '-',
                'no_hp'  => $p->no_hp  ?? '-',
                'rt_rw'  => $p->rt_rw  ?? '-',
                'desa'   => $p->desa   ?? '-',
                'kecamatan'  => $p->kecamatan  ?? '-',
                'kabupaten'  => $p->kabupaten  ?? '-',
                'provinsi'   => $p->provinsi   ?? '-',
                'no_ktp'     => $p->no_ktp     ?? '-',
                'nomor_meteran'       => $p->nomor_meteran       ?? '-',
                'meteran_awal'        => $p->meteran_awal        ?? 0,
                'tanggal_daftar'      => $p->tanggal_daftar
                    ? $p->tanggal_daftar->format('d M Y')
                    : '-',
                'status'     => $p->status,
            ])->values(),
            'pagination' => [
                'total'        => $total,
                'per_page'     => $perPage,
                'current_page' => $page,
                'last_page'    => $lastPage ?: 1,
            ],
        ]);
    }

    /**
     * Detail satu pelanggan untuk ditampilkan di modal.
     */
    public function detailPelanggan(Pelanggan $pelanggan)
    {
        $pelanggan->load('jorong', 'petugas');

        return response()->json([
            'id'             => $pelanggan->id,
            'nomor'          => $pelanggan->nomor_pelanggan,
            'nama'           => $pelanggan->nama_pelanggan,
            'alamat'         => $pelanggan->alamat ?? '-',
            'rt_rw'          => $pelanggan->rt_rw  ?? '-',
            'desa'           => $pelanggan->desa   ?? '-',
            'kecamatan'      => $pelanggan->kecamatan  ?? '-',
            'kabupaten'      => $pelanggan->kabupaten  ?? '-',
            'provinsi'       => $pelanggan->provinsi   ?? '-',
            'jorong'         => $pelanggan->jorong->nama_jorong ?? '-',
            'no_hp'          => $pelanggan->no_hp   ?? '-',
            'no_ktp'         => $pelanggan->no_ktp  ?? '-',
            'nomor_meteran'        => $pelanggan->nomor_meteran        ?? '-',
            'meteran_awal'         => $pelanggan->meteran_awal         ?? 0,
            'tanggal_daftar' => $pelanggan->tanggal_daftar
                ? \Carbon\Carbon::parse($pelanggan->tanggal_daftar)->format('d M Y')
                : '-',
            'status'               => $pelanggan->status,
            'status_registrasi'    => $pelanggan->status_registrasi ?? '-',
            'petugas'              => $pelanggan->petugas->nama_petugas ?? '-',
            'latitude'             => $pelanggan->latitude  ?? null,
            'longitude'            => $pelanggan->longitude ?? null,
            'maps_url'             => $pelanggan->hasKoordinat() ? $pelanggan->googleMapsUrl() : null,
        ]);
    }
}
