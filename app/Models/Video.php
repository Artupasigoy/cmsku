<?php

namespace App\Models;

use App\Traits\HasOwnership;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model
{
    use HasFactory, SoftDeletes, HasOwnership;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'video_url',
        'embed_code',
        'thumbnail',
        'source',
        'video_id',
        'duration',
        'kategori_id',
        'views_count',
        'order',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'views_count' => 'integer',
        'order' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    // Helper untuk extract YouTube video ID dari URL
    public function getYoutubeIdAttribute(): ?string
    {
        if ($this->source !== 'youtube' || empty($this->video_url)) {
            return null;
        }

        preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]+)/', $this->video_url, $matches);
        return $matches[1] ?? null;
    }
}
