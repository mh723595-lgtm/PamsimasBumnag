<?php
// ============================================================
// app/Models/User.php
// ============================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'is_active'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    public function pelanggan() { return $this->hasOne(Pelanggan::class); }
    public function petugas() { return $this->hasOne(Petugas::class); }
    public function notifikasi() { return $this->hasMany(Notifikasi::class); }
    public function aktivitasLog() { return $this->hasMany(AktivitasLog::class); }

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isPetugas(): bool { return $this->role === 'petugas'; }
    public function isPelanggan(): bool { return $this->role === 'pelanggan'; }

    public function notifBelumDibaca() {
        return $this->notifikasi()->where('sudah_dibaca', false)->count();
    }
}
