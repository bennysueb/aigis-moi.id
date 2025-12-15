<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Image\Enums\Fit;

class Post extends Model implements HasMedia
{
    use HasFactory, HasTranslations, InteractsWithMedia, HasSlug;

    public $translatable = ['title', 'content', 'name'];

    protected $fillable = [
        'title',
        'slug',
        'user_id',
        'category_id',
        'subcategory_id',
        'type',
        'content',
        'media_url',
        'source_name',
        'source_url',
        'source_favicon_url',
        'visibility_options',
        'seo_meta',
        'published_at',
        'featured_image_drive_id'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'visibility_options' => 'array',
        'seo_meta' => 'array',
    ];
    
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title') // Generate slug dari judul
            ->saveSlugsTo('slug');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_post');
    }


    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(100)
            ->height(100)
            ->fit(Fit::Crop, 100, 100);

        $this->addMediaConversion('card-banner')
            ->width(400)
            ->height(250)
            ->fit(Fit::Crop, 400, 250);

        $this->addMediaConversion('page-banner')
            ->width(900)
            ->height(600)
            ->fit(Fit::Crop, 900, 600);
    }
    
    // --- SMART ACCESSOR: THUMBNAIL URL ---
    public function getThumbnailUrlAttribute()
    {
        // 1. Cek apakah menggunakan Google Drive?
        if (!empty($this->featured_image_drive_id)) {
            // Gunakan Route Streamer Publik (Cache)
            return route('media.stream.public', ['path' => $this->featured_image_drive_id]);
        }

        // 2. Jika tidak, cek apakah ada media Lokal (Spatie)?
        if ($this->hasMedia('thumbnail')) {
            return $this->getFirstMediaUrl('thumbnail');
        }

        // 3. Fallback
        return asset('images/placeholder-news.jpg'); 
    }
    
    // Helper untuk cek sumber (untuk UI Admin)
    public function getThumbnailSourceAttribute()
    {
        if (!empty($this->featured_image_drive_id)) return 'drive';
        if ($this->hasMedia('thumbnail')) return 'local';
        return 'none';
    }
}
