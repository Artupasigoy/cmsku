<?php

namespace App\Models;

use App\Traits\HasOwnership;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HalamanStatis extends Model
{
    use HasFactory, SoftDeletes, HasOwnership;

    protected $table = 'halaman_statis';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'thumbnail',
        'layout',
        'is_active',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
