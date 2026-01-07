<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Pengaturan extends Model
{
    protected $fillable = [
        'key',
        'group',
        'label',
        'value',
        'type',
        'options',
        'description',
        'order',
        'is_public',
    ];

    protected $casts = [
        'options' => 'array',
        'is_public' => 'boolean',
        'order' => 'integer',
    ];

    // Helper untuk ambil setting by key
    public static function get(string $key, $default = null)
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    // Helper untuk set setting value
    public static function set(string $key, $value): bool
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        Cache::forget("setting_{$key}");

        return $setting->wasRecentlyCreated || $setting->wasChanged();
    }

    // Clear cache saat update
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($model) {
            Cache::forget("setting_{$model->key}");
        });

        static::deleted(function ($model) {
            Cache::forget("setting_{$model->key}");
        });
    }
}
