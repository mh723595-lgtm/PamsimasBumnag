<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggan';

    protected $fillable = [
        'user_id',
        'jorong_id',
        'petugas_id',
        'nomor_pelanggan',
        'nama_pelanggan',
        'alamat',
        'rt_rw',
        'desa',
        'provinsi',
        'kecamatan',
        'kabupaten',
        'no_hp',
        'no_ktp',
        'meteran_awal',
        'nomor_meteran',
        'nomor_pelanggan_external',
        'latitude',
        'longitude',
        'status',
        'tanggal_daftar',
        'status_registrasi',
        'catatan_registrasi',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'tanggal_daftar' => 'date',
        'meteran_awal'   => 'integer',
        'approved_at'    => 'datetime',
        'latitude'       => 'decimal:7',
        'longitude'      => 'decimal:7',
    ];

    // ── Relations ──────────────────────────────────────────────
    public function user()        { return $this->belongsTo(User::class); }
    public function jorong()      { return $this->belongsTo(Jorong::class); }
    public function petugas()     { return $this->belongsTo(Petugas::class); }
    public function meteranAir()  { return $this->hasMany(MeteranAir::class); }
    public function tagihanAir()  { return $this->hasMany(TagihanAir::class); }
    public function pembayaran()  { return $this->hasMany(Pembayaran::class); }
    public function pengaduan()   { return $this->hasMany(Pengaduan::class); }
    public function approvedBy()  { return $this->belongsTo(User::class, 'approved_by'); }

    // ── Shortcuts ─────────────────────────────────────────────
    public function meteranTerakhir()
    {
        return $this->meteranAir()->orderByDesc('tahun')->orderByDesc('bulan')->first();
    }

    public function tagihanBelumBayar()
    {
        return $this->tagihanAir()->whereIn('status', ['belum_bayar', 'terlambat'])->get();
    }

    public function totalTunggakan(): float
    {
        return $this->tagihanAir()
            ->whereIn('status', ['belum_bayar', 'terlambat'])
            ->sum('total_bayar');
    }

    public function hasKoordinat(): bool
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    public function googleMapsUrl(): string
    {
        if ($this->hasKoordinat()) {
            return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
        }
        return '#';
    }

    public function isAktif(): bool  { return $this->status === 'aktif'; }
    public function isPending(): bool { return $this->status_registrasi === 'pending'; }
}