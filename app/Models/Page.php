<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations; // <-- Impor trait

class Page extends Model
{
    use HasFactory, HasTranslations; // <-- Gunakan trait

    // Tentukan kolom mana saja yang bisa diterjemahkan
    public $translatable = ['title', 'content'];

    // Tentukan kolom yang boleh diisi secara massal
    protected $fillable = [
        'title',
        'slug',
        'content',
        'status',
    ];
}
