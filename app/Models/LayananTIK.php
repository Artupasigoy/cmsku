<?php

namespace App\Models;

use App\Traits\HasOwnership;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LayananTIK extends Model
{
    use HasFactory, SoftDeletes, HasOwnership;

    protected $table = 'layanan_t_i_k_s';

    protected $fillable = [
        'nama_layanan',
        'slug',
        'deskripsi',
        'icon',
        'thumbnail',
        'url',
        'kategori',
        'order',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];
}
