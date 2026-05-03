<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * @property string|null $pemakaian
 * @property string|null $angka_awal
 * @property string|null $angka_akhir
 */

class MeteranAir extends Model
{
    use HasFactory;

    protected $table = 'meteran_air';

    protected $fillable = [
        'pelanggan_id', 'petugas_id',
        'bulan', 'tahun',
        'angka_awal', 'angka_akhir', 'pemakaian',
        'tanggal_baca', 'keterangan', 'foto_meter',
    ];

    protected $casts = [
        'tanggal_baca' => 'date',
        'angka_awal'   => 'decimal:2',
        'angka_akhir'  => 'decimal:2',
        'pemakaian'    => 'decimal:2',
        'bulan'        => 'integer',
        'tahun'        => 'integer',
    ];

    // ── Auto-hitung pemakaian sebelum simpan ──────────────────
    protected static function booted(): void
    {
        static::creating(function (self $m) {
            $m->pemakaian = max(0, (float)$m->angka_akhir - (float)$m->angka_awal);
        });
        static::updating(function (self $m) {
            $m->pemakaian = max(0, (float)$m->angka_akhir - (float)$m->angka_awal);
        });
    }

    // ── Relations ─────────────────────────────────────────────
    public function pelanggan() { return $this->belongsTo(Pelanggan::class); }
    public function petugas()   { return $this->belongsTo(Petugas::class); }
    public function tagihan()   { return $this->hasOne(TagihanAir::class, 'meteran_id'); }

    // ── Helpers ───────────────────────────────────────────────
    public function periodeTeks(): string
    {
        return \App\Services\TagihanService::namaBulan($this->bulan) . ' ' . $this->tahun;
    }

    public function hasFoto(): bool
    {
        return !empty($this->foto_meter);
    }

    public function fotoUrl(): ?string
    {
        return $this->foto_meter ? Storage::url($this->foto_meter) : null;
    }
}
