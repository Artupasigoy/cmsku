<?php

namespace App\Models;

use App\Traits\HasOwnership;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OpenData extends Model
{
    use HasFactory, SoftDeletes, HasOwnership;

    protected $table = 'open_data';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'file_path',
        'file_name',
        'format',
        'license',
        'source',
        'tahun',
        'download_count',
        'is_active',
    ];

    protected $casts = [
        'download_count' => 'integer',
        'is_active' => 'boolean',
    ];
}
