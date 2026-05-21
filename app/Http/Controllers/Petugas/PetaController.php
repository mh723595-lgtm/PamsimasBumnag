<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;

class PetaController extends Controller
{
    public function index()
    {
        $pelangganDenganKoordinat = Pelanggan::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(function ($p) {
                $p->total_tunggakan = $p->totalTunggakan();
                return $p;
            });

        $pelangganTanpaKoordinat = Pelanggan::where(function($q) {
            $q->whereNull('latitude')->orWhereNull('longitude');
        })->get();

        $totalPelanggan = Pelanggan::count();
        $adaKoordinat   = $pelangganDenganKoordinat->count();
        $belumKoordinat = $pelangganTanpaKoordinat->count();

        return view('petugas.peta', compact(
            'pelangganDenganKoordinat',
            'pelangganTanpaKoordinat',
            'totalPelanggan',
            'adaKoordinat',
            'belumKoordinat'
        ));
    }
}