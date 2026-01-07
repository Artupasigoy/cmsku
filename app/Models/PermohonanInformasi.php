<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermohonanInformasi extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nomor_registrasi',
        'nama_pemohon',
        'nik',
        'email',
        'telepon',
        'alamat',
        'pekerjaan',
        'rincian_informasi',
        'tujuan_penggunaan',
        'cara_memperoleh',
        'cara_mendapat_salinan',
        'status',
        'tanggal_permohonan',
        'tanggal_respon',
        'catatan_admin',
        'file_dokumen',
        'handled_by',
    ];

    protected $casts = [
        'tanggal_permohonan' => 'datetime',
        'tanggal_respon' => 'datetime',
    ];

    public function handler(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'handled_by');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->nomor_registrasi)) {
                $model->nomor_registrasi = 'PPID-' . date('Ymd') . '-' . str_pad(static::count() + 1, 4, '0', STR_PAD_LEFT);
            }
            if (empty($model->tanggal_permohonan)) {
                $model->tanggal_permohonan = now();
            }
        });
    }
}
