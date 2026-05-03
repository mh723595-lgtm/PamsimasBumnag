<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AktivitasLog extends Model
{
    use HasFactory;

    protected $table = 'aktivitas_log';

    protected $fillable = [
        'user_id',
        'aksi',
        'model',
        'model_id',
        'deskripsi',
        'ip_address',
        'user_agent',
    ];

    // ── Relations ──────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Static helper ─────────────────────────────────────────
    /**
     * Catat aktivitas sistem
     *
     * @param  string       $aksi        Nama aksi singkat, e.g. 'create_tagihan'
     * @param  string       $deskripsi   Deskripsi lengkap
     * @param  string|null  $model       Nama model terkait, e.g. 'TagihanAir'
     * @param  int|null     $modelId     ID record terkait
     */
    public static function catat(
        string  $aksi,
        string  $deskripsi,
        ?string $model   = null,
        ?int    $modelId = null
    ): void {
        self::create([
            'user_id'    => Auth::id(),
            'aksi'       => $aksi,
            'model'      => $model,
            'model_id'   => $modelId,
            'deskripsi'  => $deskripsi,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
