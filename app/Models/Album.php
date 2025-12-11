<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use App\Models\AlbumDrivePhoto;
use Illuminate\Support\Collection;


class Album extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, HasSlug;

    protected $fillable = [
        'name',
        'slug',
    ];

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumbnail')
            ->width(200)
            ->height(200)
            ->sharpen(10);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name') // <-- Buat slug dari kolom 'name'
            ->saveSlugsTo('slug');      // <-- Simpan slug ke kolom 'slug'
    }
    
    public function drivePhotos()
    {
        return $this->hasMany(AlbumDrivePhoto::class);
    }
    
    public function getAllPhotosAttribute()
    {
        // A. Ambil Media Lokal (Spatie)
        $local = $this->getMedia()->toBase()->map(function ($item) {
            return [
                'id'         => $item->id,
                'source'     => 'local',
                // URL Asli untuk Fancybox
                'url'        => $item->getUrl(), 
                // Thumbnail (jika ada konversi, jika tidak pakai url asli)
                'thumb'      => $item->hasGeneratedConversion('thumbnail') ? $item->getUrl('thumbnail') : $item->getUrl(),
                'name'       => $item->name,
                'created_at' => $item->created_at,
            ];
        });

        // B. Ambil Media Drive (Tabel Baru)
        $drive = $this->drivePhotos()->get()->toBase()->map(function ($item) {
            $streamUrl = route('media.stream.public', ['path' => $item->file_id]);
            
            return [
                'id'         => $item->id,
                'source'     => 'drive',
                // URL Stream untuk Fancybox
                'url'        => $streamUrl,
                // Google Drive sulit generate thumb tanpa download, pakai gambar asli saja
                'thumb'      => $streamUrl, 
                'name'       => $item->file_name ?? 'Drive Image',
                'created_at' => $item->created_at,
            ];
        });

        // C. Gabung dan Urutkan (Terbaru di atas)
        return $local->merge($drive)->sortByDesc('created_at');
    }
}
