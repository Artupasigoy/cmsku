<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Model SystemSetting untuk konfigurasi website via database
 * Memungkinkan perubahan konfigurasi tanpa developer
 * 
 * @property int $id
 * @property string $key
 * @property string|null $value
 * @property string $type
 * @property string $group
 * @property string|null $description
 * @property bool $is_public
 */
class SystemSetting extends Model
{
    /**
     * Atribut yang bisa diisi secara massal
     */
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
        'is_public',
    ];

    /**
     * Konfigurasi casting atribut
     */
    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
        ];
    }

    /**
     * Cache key prefix untuk settings
     */
    protected static string $cachePrefix = 'system_setting_';

    /**
     * Dapatkan nilai setting berdasarkan key
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        return Cache::rememberForever(self::$cachePrefix . $key, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();

            if (!$setting) {
                return $default;
            }

            return self::castValue($setting->value, $setting->type);
        });
    }

    /**
     * Set nilai setting
     * 
     * @param string $key
     * @param mixed $value
     * @param string $type
     * @param string $group
     * @param string|null $description
     * @return self
     */
    public static function set(string $key, $value, string $type = 'string', string $group = 'general', ?string $description = null): self
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value) : $value,
                'type' => $type,
                'group' => $group,
                'description' => $description,
            ]
        );

        // Clear cache
        Cache::forget(self::$cachePrefix . $key);

        return $setting;
    }

    /**
     * Cast nilai berdasarkan tipe
     */
    protected static function castValue($value, string $type)
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'json' => json_decode($value, true),
            default => $value,
        };
    }

    /**
     * Dapatkan semua settings berdasarkan group
     */
    public static function getByGroup(string $group): array
    {
        return self::where('group', $group)
            ->get()
            ->mapWithKeys(fn($setting) => [
                $setting->key => self::castValue($setting->value, $setting->type)
            ])
            ->toArray();
    }

    /**
     * Clear semua cache settings
     */
    public static function clearAllCache(): void
    {
        self::all()->each(function ($setting) {
            Cache::forget(self::$cachePrefix . $setting->key);
        });
    }

    /**
     * Boot method untuk auto-clear cache saat update
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($model) {
            Cache::forget(self::$cachePrefix . $model->key);
        });

        static::deleted(function ($model) {
            Cache::forget(self::$cachePrefix . $model->key);
        });
    }
}
