<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
// ▼▼▼ PASTIKAN ANDA MENGGUNAKAN IMPORT YANG BENAR INI ▼▼▼
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class InquirySubmission extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'inquiry_form_id',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function form()
    {
        return $this->belongsTo(InquiryForm::class, 'inquiry_form_id');
    }

    /**
     * Daftarkan konversi media (thumbnail).
     * Perhatikan type-hint `Media` di sini sekarang sudah benar.
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
              ->width(200)
              ->height(200)
              ->sharpen(10);
    }
}