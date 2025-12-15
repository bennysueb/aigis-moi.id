<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Collaborator extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $guarded = ['id'];

    // Relasi balik ke Kategori
    public function category()
    {
        return $this->belongsTo(CollaboratorCategory::class, 'collaborator_category_id');
    }

    // Helper untuk mengambil URL Logo
    // Memilih antara gambar upload (Spatie) atau URL remote
    public function getLogoUrlAttribute()
    {
        if ($this->logo_type === 'url' && !empty($this->logo_url_remote)) {
            return $this->logo_url_remote;
        }

        // Ambil dari Spatie Media Library (collection 'logo')
        return $this->getFirstMediaUrl('logo');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')
            ->singleFile(); // Memastikan 1 perusahaan hanya punya 1 logo aktif
    }
}
