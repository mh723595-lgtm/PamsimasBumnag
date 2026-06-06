<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jorong extends Model
{
    protected $table = 'jorong';

    protected $fillable = [
        'nama_jorong',
        'kode_jorong',
        'provinsi',
        'kabupaten',
        'kecamatan',
        'desa',
        'nagari',
        'keterangan',
        'aktif',
        'dibuat_oleh',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    public function assignPetugas()
    {
        return $this->hasMany(AssignPetugas::class);
    }

    public function pelanggan()
    {
        return $this->hasMany(Pelanggan::class);
    }
}
