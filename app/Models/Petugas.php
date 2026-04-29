<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'status_registrasi',
        'catatan_registrasi',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
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

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ── Helpers ───────────────────────────────────────────────
    public function totalInputBulanIni(): int
    {
        return $this->meteranAir()
            ->whereMonth('tanggal_baca', now()->month)
            ->whereYear('tanggal_baca', now()->year)
            ->count();
    }

    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }

    public function isPending(): bool
    {
        return $this->status_registrasi === 'pending';
    }
}
