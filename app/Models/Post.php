<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use App\Services\ContentSanitizer;

class Post extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'image',
        'status',
        'published_at',
        'meta_title',
        'meta_description',
        'author_id',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->where('published_at', '<=', now());
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    // Automatically sanitize content when setting
    public function setContentAttribute($value)
    {
        $sanitizer = new ContentSanitizer();
        $this->attributes['content'] = $sanitizer->sanitize($value);
    }

    // Get sanitized content for public display
    public function getSanitizedContentAttribute()
    {
        $sanitizer = new ContentSanitizer();
        return $sanitizer->sanitizeForDisplay($this->content);
    }
}
