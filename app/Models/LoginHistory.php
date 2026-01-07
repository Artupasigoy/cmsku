<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model LoginHistory untuk mencatat percobaan login
 * Digunakan untuk audit keamanan dan monitoring
 * 
 * @property int $id
 * @property int|null $user_id
 * @property string|null $email
 * @property string $ip_address
 * @property string|null $user_agent
 * @property string|null $location
 * @property bool $success
 * @property string|null $failure_reason
 * @property \Carbon\Carbon $attempted_at
 */
class LoginHistory extends Model
{
    /**
     * Nonaktifkan timestamps default karena menggunakan attempted_at
     */
    public $timestamps = false;

    /**
     * Atribut yang bisa diisi secara massal
     */
    protected $fillable = [
        'user_id',
        'email',
        'ip_address',
        'user_agent',
        'location',
        'success',
        'failure_reason',
        'attempted_at',
    ];

    /**
     * Konfigurasi casting atribut
     */
    protected function casts(): array
    {
        return [
            'success' => 'boolean',
            'attempted_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope untuk login yang berhasil
     */
    public function scopeSuccessful($query)
    {
        return $query->where('success', true);
    }

    /**
     * Scope untuk login yang gagal
     */
    public function scopeFailed($query)
    {
        return $query->where('success', false);
    }

    /**
     * Scope untuk filter berdasarkan rentang waktu
     */
    public function scopeWithinDays($query, int $days)
    {
        return $query->where('attempted_at', '>=', now()->subDays($days));
    }
}
