<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\TagihanAir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $petugas = Auth::user()->petugas;

        $query = Pelanggan::where('petugas_id', $petugas?->id)
            ->with(['jorong', 'user'])
            ->orderBy('nomor_pelanggan');

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Pencarian nama / nomor
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) =>
                $q->where('nama_pelanggan', 'like', "%$s%")
                  ->orWhere('nomor_pelanggan', 'like', "%$s%")
                  ->orWhere('no_hp', 'like', "%$s%")
            );
        }

        $pelangganList = $query->paginate(15)->withQueryString();

        // Statistik cepat
        $stats = [
            'total'   => Pelanggan::where('petugas_id', $petugas?->id)->count(),
            'aktif'   => Pelanggan::where('petugas_id', $petugas?->id)->where('status', 'aktif')->count(),
            'tunggakan' => TagihanAir::whereIn('pelanggan_id',
                    Pelanggan::where('petugas_id', $petugas?->id)->pluck('id')
                )->whereIn('status', ['belum_bayar', 'terlambat'])->count(),
        ];

        return view('petugas.pelanggan.index', compact('pelangganList', 'stats', 'petugas'));
    }
}