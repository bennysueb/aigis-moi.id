<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GalleryVideo extends Model
{
    use HasFactory;

    protected $fillable = [
        'video_gallery_id',
        'series_title',
        'youtube_embed_url',
        'order',
    ];

    /**
     * Sebuah video dimiliki oleh sebuah galeri.
     */
    public function gallery(): BelongsTo
    {
        return $this->belongsTo(VideoGallery::class, 'video_gallery_id');
    }
}
