<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property string|null $total_tagihan
 * @property string|null $denda
 * @property string|null $total_bayar
 */

class TagihanAir extends Model
{
    use HasFactory;

    protected $table = 'tagihan_air';

    protected $fillable = [
        'pelanggan_id', 'meteran_id', 'nomor_tagihan',
        'bulan', 'tahun', 'pemakaian',
        'total_tagihan', 'denda', 'total_bayar', 'tanggal_denda',
        'tanggal_tagihan', 'tanggal_jatuh_tempo', 'status',
    ];

    protected $casts = [
        'tanggal_tagihan'     => 'date',
        'tanggal_jatuh_tempo' => 'date',
        'tanggal_denda'       => 'date',
        'pemakaian'           => 'decimal:2',
        'total_tagihan'       => 'decimal:2',
        'denda'               => 'decimal:2',
        'total_bayar'         => 'decimal:2',
        'bulan'               => 'integer',
        'tahun'               => 'integer',
    ];

    // ── Relations ──────────────────────────────────────────────
    public function pelanggan() { return $this->belongsTo(Pelanggan::class); }
    public function meteran()   { return $this->belongsTo(MeteranAir::class, 'meteran_id'); }
    public function pembayaran(){ return $this->hasOne(Pembayaran::class, 'tagihan_id'); }

    // ── Auto-set total_bayar sebelum simpan ───────────────────
    protected static function booted(): void
    {
        static::saving(function (self $model) {
            $model->total_bayar = ($model->total_tagihan ?? 0) + ($model->denda ?? 0);
        });
    }

    // ── Accessor: total yang harus dibayar ────────────────────
    public function getTotalHarusBayarAttribute(): float
    {
        return (float) $this->total_tagihan + (float) $this->denda;
    }

    // ── Status helpers ────────────────────────────────────────
    public function isLunas(): bool    { return $this->status === 'lunas'; }
    public function isBelumBayar():bool{ return $this->status === 'belum_bayar'; }
    public function isTerlambat(): bool{ return $this->status === 'terlambat'; }
    public function hasDenda(): bool   { return (float) $this->denda > 0; }

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

    // ── Scopes ───────────────────────────────────────────────
    public function scopeLunas(Builder $q)      { return $q->where('status', 'lunas'); }
    public function scopeBelumLunas(Builder $q) { return $q->whereIn('status', ['belum_bayar', 'terlambat']); }
    public function scopePeriode(Builder $q, int $bulan, int $tahun) {
        return $q->where('bulan', $bulan)->where('tahun', $tahun);
    }
    public function scopeTerlambat(Builder $q) {
        return $q->where('status', 'terlambat')
                 ->where('tanggal_jatuh_tempo', '<', now()->toDateString());
    }
}
