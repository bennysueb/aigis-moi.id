<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Image\Enums\Fit;

class Banner extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'headline',
        'subtitle',
        'features',
        'button_text',
        'button_link',
        'order',
        'is_active',
        'gradient_from',
        'gradient_to',
        'opacity',
        'position',
        'url',

    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    public function getDesktopImageUrlAttribute()
    {
        if ($this->hasMedia('desktop_image')) {
            return $this->getFirstMediaUrl('desktop_image');
        }
        return 'https://via.placeholder.com/1200x800.png/002B5B?text=Desktop+Banner';
    }

    public function getMobileImageUrlAttribute()
    {
        // Jika gambar mobile tidak ada, gunakan gambar desktop sebagai fallback
        if ($this->hasMedia('mobile_image')) {
            return $this->getFirstMediaUrl('mobile_image');
        }
        return $this->desktop_image_url;
    }

    public function registerMediaConversions(Media $media = null): void
    {
        // Versi Lebar
        $this->addMediaConversion('ad-wide')
            ->fit(Fit::Crop, 720, 90); // <-- DIPERBARUI

        // Versi Kotak
        $this->addMediaConversion('ad-square')
            ->fit(Fit::Crop, 480, 480); // <-- DIPERBARUI

        // Versi Tinggi
        $this->addMediaConversion('ad-tall')
            ->fit(Fit::Crop, 240, 640); // <-- DIPERBARUI
    }
}
