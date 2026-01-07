<?php

namespace App\Models;

use App\Traits\HasOwnership;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Statistik extends Model
{
    use HasFactory, SoftDeletes, HasOwnership;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'value',
        'unit',
        'period',
        'source',
        'icon',
        'color',
        'order',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];
}
