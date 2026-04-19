<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeteranAir extends Model
{
    use HasFactory;

    protected $table = 'meteran_air';

    protected $fillable = [
        'pelanggan_id',
        'petugas_id',
        'bulan',
        'tahun',
        'angka_awal',
        'angka_akhir',
        'pemakaian',
        'tanggal_baca',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_baca' => 'date',
        'angka_awal'   => 'float',
        'angka_akhir'  => 'float',
        'pemakaian'    => 'float',
        'bulan'        => 'integer',
        'tahun'        => 'integer',
    ];

    // ── Auto-hitung pemakaian sebelum simpan ──────────────────
    protected static function booted(): void
    {
        static::creating(function (self $model) {
            $awal  = (float) ($model->angka_awal ?? 0);
            $akhir = (float) ($model->angka_akhir ?? 0);

            $model->pemakaian = max(0, $akhir - $awal);
        });

        static::updating(function (self $model) {
            $awal  = (float) ($model->angka_awal ?? 0);
            $akhir = (float) ($model->angka_akhir ?? 0);

            $model->pemakaian = max(0, $akhir - $awal);
        });
    }

    // ── Relations ──────────────────────────────────────────────
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function petugas()
    {
        return $this->belongsTo(Petugas::class);
    }

    public function tagihan()
    {
        return $this->hasOne(TagihanAir::class, 'meteran_id');
    }

    // ── Helpers ────────────────────────────────────────────────
    public function periodeTeks(): string
    {
        return \App\Services\TagihanService::namaBulan($this->bulan) . ' ' . $this->tahun;
    }
}
