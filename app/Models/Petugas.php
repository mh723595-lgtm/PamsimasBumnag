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
        'user_id', 'nip', 'nik', 'nama_petugas', 'jabatan',
        'no_hp', 'alamat', 'foto', 'tanggal_lahir', 'tmt',
        'status', 'status_registrasi', 'catatan_registrasi',
        'approved_at', 'approved_by',
    ];

    protected $casts = [
        'approved_at'   => 'datetime',
        'tanggal_lahir' => 'date',
        'tmt'           => 'date',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function meteranAir() { return $this->hasMany(MeteranAir::class); }
    public function approvedBy() { return $this->belongsTo(User::class, 'approved_by'); }

    public function totalInputBulanIni(): int
    {
        return $this->meteranAir()
            ->whereMonth('tanggal_baca', now()->month)
            ->whereYear('tanggal_baca', now()->year)
            ->count();
    }

    public function isAktif(): bool { return $this->status === 'aktif'; }
    public function isPending(): bool { return $this->status_registrasi === 'pending'; }

    // Hitung lama bergabung dari TMT
    public function lamaBergabung(): string
    {
        if (!$this->tmt) return '-';
        $diff = Carbon::parse($this->tmt)->diff(Carbon::now());
        $parts = [];
        if ($diff->y > 0) $parts[] = $diff->y . ' tahun';
        if ($diff->m > 0) $parts[] = $diff->m . ' bulan';
        if (empty($parts))  $parts[] = $diff->d . ' hari';
        return implode(' ', $parts);
    }

    // Cek apakah hari ini ulang tahun
    public function isUlangTahunHariIni(): bool
    {
        if (!$this->tanggal_lahir) return false;
        return Carbon::parse($this->tanggal_lahir)->format('m-d')
    === now()->format('m-d');
    }

    // URL foto profil
    public function fotoUrl(): string
    {
        return $this->foto
            ? asset('storage/' . $this->foto)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->nama_petugas) . '&background=3b93f2&color=fff&size=128';
    }
}