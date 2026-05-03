<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'notifikasi';

    protected $fillable = [
        'user_id',
        'judul',
        'pesan',
        'tipe',
        'url',
        'sudah_dibaca',
    ];

    protected $casts = [
        'sudah_dibaca' => 'boolean',
    ];

    // ── Relations ──────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Helpers ────────────────────────────────────────────────
    public function tipeBadge(): string
    {
        return match ($this->tipe) {
            'success' => 'bg-emerald-100 text-emerald-800',
            'warning' => 'bg-amber-100 text-amber-800',
            'error'   => 'bg-red-100 text-red-800',
            default   => 'bg-brand-100 text-brand-800',
        };
    }

    public function tipeIcon(): string
    {
        return match ($this->tipe) {
            'success' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            'warning' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
            'error'   => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
            default   => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        };
    }

    // ── Scopes ────────────────────────────────────────────────
    public function scopeBelumDibaca(Builder $query)
    {
        return $query->where('sudah_dibaca', false);
    }

    // ── Static helpers ────────────────────────────────────────
    public static function kirim(int $userId, string $judul, string $pesan, string $tipe = 'info', ?string $url = null): self
    {
        return self::create([
            'user_id'     => $userId,
            'judul'       => $judul,
            'pesan'       => $pesan,
            'tipe'        => $tipe,
            'url'         => $url,
            'sudah_dibaca'=> false,
        ]);
    }
}
