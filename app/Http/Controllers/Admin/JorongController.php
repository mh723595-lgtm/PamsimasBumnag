<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jorong;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JorongController extends Controller
{
    public function index()
    {
        $jorong = Jorong::orderBy('nama_jorong')->get();
        return view('admin.jorong.index', compact('jorong'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_jorong'  => 'required|string|max:100|unique:jorong,nama_jorong',
            'kode_jorong'  => 'nullable|string|max:20|unique:jorong,kode_jorong',
            'keterangan'   => 'nullable|string|max:255',
        ]);

        Jorong::create([
            'nama_jorong'  => $request->nama_jorong,
            'kode_jorong'  => $request->kode_jorong,
            'keterangan'   => $request->keterangan,
            'aktif'        => true,
            'dibuat_oleh'  => Auth::id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Jorong berhasil ditambahkan!']);
    }

    public function update(Request $request, Jorong $jorong)
    {
        $request->validate([
            'nama_jorong' => 'required|string|max:100|unique:jorong,nama_jorong,' . $jorong->id,
            'kode_jorong' => 'nullable|string|max:20|unique:jorong,kode_jorong,' . $jorong->id,
            'keterangan'  => 'nullable|string|max:255',
        ]);

        $jorong->update([
            'nama_jorong' => $request->nama_jorong,
            'kode_jorong' => $request->kode_jorong,
            'keterangan'  => $request->keterangan,
        ]);

        return response()->json(['success' => true, 'message' => 'Jorong berhasil diupdate!']);
    }

    public function destroy(Jorong $jorong)
    {
        // Cek apakah jorong masih dipakai
        if ($jorong->assignPetugas()->count() > 0) {
            return response()->json(['success' => false, 'message' => 'Jorong tidak dapat dihapus karena masih ada petugas yang diassign!']);
        }

        $jorong->delete();
        return response()->json(['success' => true, 'message' => 'Jorong berhasil dihapus!']);
    }

    public function toggleAktif(Jorong $jorong)
    {
        $jorong->update(['aktif' => !$jorong->aktif]);
        return response()->json([
            'success' => true,
            'aktif'   => $jorong->aktif,
            'message' => 'Status jorong berhasil diubah!'
        ]);
    }
}