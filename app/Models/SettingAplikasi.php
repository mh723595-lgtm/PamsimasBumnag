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

    /**
     * Prefix cache key dan TTL (1 jam).
     * Semua setting di-cache sekaligus sebagai satu array untuk efisiensi.
     */
    const CACHE_KEY = 'setting_aplikasi_all';
    const CACHE_TTL = 3600; // detik

    // ── Cache helpers ─────────────────────────────────────────

    /**
     * Invalidasi cache. Dipanggil setiap kali ada perubahan setting.
     */
    public static function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Ambil seluruh setting sebagai array key => value (dengan cache).
     */
    protected static function allCached(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return self::pluck('value', 'key')->toArray();
        });
    }

    // ── Static helpers ────────────────────────────────────────

    /**
     * Ambil nilai setting berdasarkan key (dari cache).
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $all = self::allCached();
        return array_key_exists($key, $all) ? $all[$key] : $default;
    }

    /**
     * Simpan atau update nilai setting, lalu invalidasi cache.
     */
    public static function set(string $key, mixed $value): void
    {
        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
        self::flushCache();
    }

    /**
     * Ambil semua setting dalam satu grup (dari cache).
     */
    public static function getGrup(string $grup): array
    {
        // Filter dari cache utama agar tidak perlu query tambahan
        $setting = self::where('grup', $grup)
            ->pluck('value', 'key')
            ->toArray();
        return $setting;
    }

    /**
     * Ambil nama sistem dari setting.
     */
    public static function namaSistem(): string
    {
        return self::get('nama_sistem', 'PAMSIMAS');
    }

    /**
     * Ambil nama desa dari setting.
     */
    public static function namaDesa(): string
    {
        return self::get('nama_desa', 'Desa');
    }
}