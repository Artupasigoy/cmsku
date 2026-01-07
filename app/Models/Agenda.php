<?php

namespace App\Models;

use App\Traits\HasOwnership;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agenda extends Model
{
    use HasFactory, SoftDeletes, HasOwnership;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'location',
        'organizer',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'is_all_day',
        'status',
        'thumbnail',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_all_day' => 'boolean',
    ];
}
