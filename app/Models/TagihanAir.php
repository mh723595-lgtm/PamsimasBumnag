<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagihanAir extends Model
{
    use HasFactory;

    protected $table = 'tagihan_air';

    protected $fillable = [
        'pelanggan_id',
        'meteran_id',
        'nomor_tagihan',
        'bulan',
        'tahun',
        'pemakaian',
        'total_tagihan',
        'tanggal_tagihan',
        'tanggal_jatuh_tempo',
        'status',
    ];

    protected $casts = [
        'tanggal_tagihan'     => 'date',
        'tanggal_jatuh_tempo' => 'date',
        'pemakaian'           => 'decimal:2',
        'total_tagihan'       => 'decimal:2',
        'bulan'               => 'integer',
        'tahun'               => 'integer',
    ];

    // ── Relations ──────────────────────────────────────────────
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function meteran()
    {
        return $this->belongsTo(MeteranAir::class, 'meteran_id');
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'tagihan_id');
    }

    // ── Status helpers ─────────────────────────────────────────
    public function isLunas(): bool
    {
        return $this->status === 'lunas';
    }

    public function isBelumBayar(): bool
    {
        return $this->status === 'belum_bayar';
    }

    public function isTerlambat(): bool
    {
        return $this->status === 'terlambat';
    }

    public function statusBadge(): string
    {
        return match ($this->status) {
            'lunas'       => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300',
            'belum_bayar' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
            'terlambat'   => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
            default       => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'lunas'       => 'Lunas',
            'belum_bayar' => 'Belum Bayar',
            'terlambat'   => 'Terlambat',
            default       => ucfirst($this->status),
        };
    }

    public function periodeTeks(): string
    {
        return \App\Services\TagihanService::namaBulan($this->bulan) . ' ' . $this->tahun;
    }

    // ── Scopes ────────────────────────────────────────────────
    public function scopeLunas($query)
    {
        return $query->where('status', 'lunas');
    }

    public function scopeBelumLunas($query)
    {
        return $query->whereIn('status', ['belum_bayar', 'terlambat']);
    }

    public function scopePeriode($query, int $bulan, int $tahun)
    {
        return $query->where('bulan', $bulan)->where('tahun', $tahun);
    }
}