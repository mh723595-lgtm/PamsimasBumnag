<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Pengaduan extends Model
{
    use HasFactory;

    protected $table = 'pengaduan';

    protected $fillable = [
        'pelanggan_id',
        'nomor_pengaduan',
        'judul',
        'deskripsi',
        'foto',
        'jenis',
        'status',
        'prioritas',
        'tanggapan',
        'ditangani_oleh',
        'tanggal_selesai',
    ];

    protected $casts = [
        'tanggal_selesai' => 'datetime',
    ];

    // ── Relations ──────────────────────────────────────────────
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function ditanganiOleh()
    {
        return $this->belongsTo(User::class, 'ditangani_oleh');
    }

    // ── Helpers ────────────────────────────────────────────────
    public function statusBadge(): string
    {
        return match ($this->status) {
            'baru'     => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
            'diproses' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
            'selesai'  => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300',
            'ditolak'  => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
            default    => 'bg-gray-100 text-gray-800',
        };
    }

    public function prioritasBadge(): string
    {
        return match ($this->prioritas) {
            'tinggi' => '🔴',
            'sedang' => '🟡',
            'rendah' => '🟢',
            default  => '⚪',
        };
    }

    public function isSelesai(): bool
    {
        return $this->status === 'selesai';
    }

    public function isBaru(): bool
    {
        return $this->status === 'baru';
    }

    // ── Scopes ────────────────────────────────────────────────
    public function scopeBaru(Builder $query)
    {
        return $query->where('status', 'baru');
    }

    public function scopeAktif(Builder $query)
    {
        return $query->whereIn('status', ['baru', 'diproses']);
    }
}
