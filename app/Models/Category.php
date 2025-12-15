<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory, HasTranslations;

    public $translatable = ['name'];
    protected $fillable = ['name', 'slug', 'parent_id'];

    // Relasi ke posts (Sudah ada)
    public function posts()
    {
        return $this->belongsToMany(Post::class, 'category_post');
    }

    /**
     * Mendapatkan kategori induk (parent) dari sub-kategori ini.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Mendapatkan semua sub-kategori (children) dari kategori ini.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}
