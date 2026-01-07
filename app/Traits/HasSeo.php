<?php

namespace App\Traits;

/**
 * Trait HasSeo
 * Provides SEO-related methods and accessors for models
 * 
 * Models using this trait should have these fields:
 * - meta_title (nullable)
 * - meta_description (nullable)
 * - meta_keywords (nullable)
 * - slug (required)
 */
trait HasSeo
{
    /**
     * Get the SEO title (uses meta_title if available, otherwise title)
     */
    public function getSeoTitleAttribute(): string
    {
        return $this->meta_title ?? $this->title ?? '';
    }

    /**
     * Get the SEO description
     */
    public function getSeoDescriptionAttribute(): string
    {
        if (!empty($this->meta_description)) {
            return $this->meta_description;
        }

        // Fallback to content excerpt
        $content = $this->content ?? $this->description ?? '';
        return strip_tags(substr($content, 0, 160));
    }

    /**
     * Get the canonical URL for the model
     */
    public function getCanonicalUrlAttribute(): string
    {
        $routeName = $this->getSeoRouteName();

        if ($routeName && \Route::has($routeName)) {
            return route($routeName, $this->slug);
        }

        return url($this->getBasePath() . '/' . $this->slug);
    }

    /**
     * Get Open Graph data as array
     */
    public function getOpenGraphData(): array
    {
        return [
            'title' => $this->seo_title,
            'description' => $this->seo_description,
            'type' => 'article',
            'url' => $this->canonical_url,
            'image' => $this->getOgImage(),
            'site_name' => config('app.name'),
        ];
    }

    /**
     * Get JSON-LD structured data
     */
    public function getJsonLdData(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $this->seo_title,
            'description' => $this->seo_description,
            'url' => $this->canonical_url,
            'datePublished' => $this->published_at?->toIso8601String() ?? $this->created_at->toIso8601String(),
            'dateModified' => $this->updated_at->toIso8601String(),
            'author' => [
                '@type' => 'Organization',
                'name' => config('app.name'),
            ],
        ];
    }

    /**
     * Get OG image URL
     */
    protected function getOgImage(): ?string
    {
        if (!empty($this->thumbnail)) {
            return asset('storage/' . $this->thumbnail);
        }

        if (!empty($this->image)) {
            return asset('storage/' . $this->image);
        }

        return null;
    }

    /**
     * Get route name for canonical URL
     * Override in model if needed
     */
    protected function getSeoRouteName(): ?string
    {
        return null;
    }

    /**
     * Get base path for URL
     * Override in model if needed
     */
    protected function getBasePath(): string
    {
        return strtolower(class_basename($this));
    }
}
