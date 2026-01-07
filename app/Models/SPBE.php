<?php

namespace App\Models;

use App\Traits\HasOwnership;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SPBE extends Model
{
    use HasFactory, SoftDeletes, HasOwnership;

    protected $table = 's_p_b_e_s';

    protected $fillable = [
        'nama_aplikasi',
        'slug',
        'deskripsi',
        'url',
        'domain',
        'thumbnail',
        'kategori',
        'opd_pengelola',
        'tahun_operasional',
        'status',
        'order',
        'is_featured',
    ];

    protected $casts = [
        'tahun_operasional' => 'integer',
        'order' => 'integer',
        'is_featured' => 'boolean',
    ];
}
