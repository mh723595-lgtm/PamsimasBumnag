<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Petugas extends Model
{
    use HasFactory;

    protected $table = 'petugas';

    protected $fillable = [
        'user_id',
        'nip',
        'nama_petugas',
        'jabatan',
        'no_hp',
        'alamat',
        'status',
    ];

    // ── Relations ──────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function meteranAir()
    {
        return $this->hasMany(MeteranAir::class);
    }

    // ── Helpers ────────────────────────────────────────────────
    public function totalInputBulanIni(): int
    {
        return $this->meteranAir()
            ->where('bulan', now()->month)
            ->where('tahun', now()->year)
            ->count();
    }

    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }
}