<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Link extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'url',
        'icon',
        'group',
        'parent_id',
        'is_external',
        'open_new_tab',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_external' => 'boolean',
        'open_new_tab' => 'boolean',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Link::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Link::class, 'parent_id')->orderBy('order');
    }

    // Scope untuk link utama (tanpa parent)
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByGroup($query, string $group)
    {
        return $query->where('group', $group);
    }
}
