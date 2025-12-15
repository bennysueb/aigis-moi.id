<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VideoGallery extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'is_active'];

    /**
     * Sebuah galeri memiliki banyak video.
     */
    public function videos(): HasMany
    {
        return $this->hasMany(GalleryVideo::class)->orderBy('order');
    }
}
