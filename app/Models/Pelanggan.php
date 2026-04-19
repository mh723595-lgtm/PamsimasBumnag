<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggans';

    protected $fillable = [
        'user_id',
        'nomor_pelanggan',
        'nama_pelanggan',
        'alamat',
        'rt_rw',
        'desa',
        'kecamatan',
        'no_hp',
        'no_ktp',
        'meteran_awal',
        'status',
        'tanggal_daftar',
    ];

    protected $casts = [
        'tanggal_daftar' => 'date',
        'meteran_awal'   => 'integer',
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

    public function tagihanAir()
    {
        return $this->hasMany(TagihanAir::class);
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }

    public function pengaduan()
    {
        return $this->hasMany(Pengaduan::class);
    }

    public function meteranTerakhir()
    {
        return $this->hasOne(MeteranAir::class)->latestOfMany();
    }

    // ── Helpers ────────────────────────────────────────────────
    public function tagihanBelumBayar()
    {
        return $this->tagihanAir()->whereIn('status', ['belum_bayar', 'terlambat']);
    }

    public function totalTunggakan(): float
    {
        return $this->tagihanBelumBayar()->sum('total_tagihan');
    }

    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }
}