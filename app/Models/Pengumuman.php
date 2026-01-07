<?php

namespace App\Models;

use App\Traits\HasOwnership;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengumuman extends Model
{
    use HasFactory, SoftDeletes, HasOwnership;

    protected $table = 'pengumumen';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'thumbnail',
        'priority',
        'status',
        'published_at',
        'expired_at',
        'is_pinned',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'expired_at' => 'datetime',
        'is_pinned' => 'boolean',
    ];
}
