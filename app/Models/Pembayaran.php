<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';

    protected $fillable = [
        'tagihan_id',
        'pelanggan_id',
        'nomor_pembayaran',
        'jumlah_bayar',
        'tanggal_bayar',
        'metode_bayar',
        'bukti_bayar',
        'status',
        'dikonfirmasi_oleh',
        'catatan',
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'jumlah_bayar'  => 'decimal:2',
    ];

    // ── Relations ──────────────────────────────────────────────
    public function tagihan()
    {
        return $this->belongsTo(TagihanAir::class, 'tagihan_id');
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function dikonfirmasiOleh()
    {
        return $this->belongsTo(User::class, 'dikonfirmasi_oleh');
    }

    // ── Helpers ────────────────────────────────────────────────
    public function isKonfirmasi(): bool
    {
        return $this->status === 'konfirmasi';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function statusBadge(): string
    {
        return match ($this->status) {
            'konfirmasi' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300',
            'pending'    => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
            'ditolak'    => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
            default      => 'bg-gray-100 text-gray-800',
        };
    }

    // ── Scopes ────────────────────────────────────────────────
    public function scopeKonfirmasi(Builder $query)
    {
        return $query->where('status', 'konfirmasi');
    }

    public function scopePending(Builder $query)
    {
        return $query->where('status', 'pending');
    }
}
