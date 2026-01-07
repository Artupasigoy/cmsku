<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class MediaFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'original_name',
        'path',
        'disk',
        'mime_type',
        'extension',
        'size',
        'folder',
        'alt_text',
        'caption',
        'download_count',
        'is_public',
        'uploaded_by',
    ];

    protected $casts = [
        'size' => 'integer',
        'download_count' => 'integer',
        'is_public' => 'boolean',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'uploaded_by');
    }

    // Get full URL
    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    // Format file size
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    // Check if image
    public function getIsImageAttribute(): bool
    {
        return str_starts_with($this->mime_type ?? '', 'image/');
    }

    // Scope by folder
    public function scopeInFolder($query, ?string $folder)
    {
        return $query->where('folder', $folder);
    }

    // Scope by type
    public function scopeOfType($query, string $type)
    {
        return match ($type) {
            'image' => $query->where('mime_type', 'like', 'image/%'),
            'document' => $query->whereIn('extension', ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']),
            'video' => $query->where('mime_type', 'like', 'video/%'),
            'audio' => $query->where('mime_type', 'like', 'audio/%'),
            default => $query,
        };
    }
}
