<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Jorong;

class AssignPetugas extends Model
{
    protected $table = 'assign_petugas';

    protected $fillable = [
        'petugas_id',
        'jorong_id',
        'periode',
        'aktif',
        'catatan',
        'dibuat_oleh',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    public function petugas()
    {
        return $this->belongsTo(Petugas::class);
    }

    public function jorong()
    {
        return $this->belongsTo(Jorong::class);
    }
    public function dibuatOleh()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }
}