<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SettingAplikasi extends Model
{
    use HasFactory;

    protected $table = 'setting_aplikasi';

    protected $fillable = [
        'key',
        'value',
        'label',
        'tipe',
        'grup',
    ];

    // ── Static helpers ────────────────────────────────────────

    /**
     * Ambil nilai setting berdasarkan key
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Simpan atau update nilai setting
     */
    public static function set(string $key, mixed $value): void
    {
        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Ambil semua setting dalam satu grup
     */
    public static function getGrup(string $grup): array
    {
        return self::where('grup', $grup)
            ->pluck('value', 'key')
            ->toArray();
    }

    /**
     * Ambil nama sistem dari setting
     */
    public static function namaSistem(): string
    {
        return self::get('nama_sistem', 'PAMSIMAS');
    }

    /**
     * Ambil nama desa dari setting
     */
    public static function namaDesa(): string
    {
        return self::get('nama_desa', 'Desa');
    }
}