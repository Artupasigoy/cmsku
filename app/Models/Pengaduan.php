<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengaduan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nomor_tiket',
        'nama_pelapor',
        'email',
        'telepon',
        'alamat',
        'kategori',
        'judul',
        'isi_pengaduan',
        'lokasi_kejadian',
        'tanggal_kejadian',
        'lampiran',
        'status',
        'tanggapan',
        'tanggal_tanggapan',
        'is_anonymous',
        'handled_by',
    ];

    protected $casts = [
        'tanggal_kejadian' => 'date',
        'tanggal_tanggapan' => 'datetime',
        'lampiran' => 'array',
        'is_anonymous' => 'boolean',
    ];

    public function handler(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'handled_by');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->nomor_tiket)) {
                $model->nomor_tiket = 'ADU-' . date('Ymd') . '-' . str_pad(static::count() + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
