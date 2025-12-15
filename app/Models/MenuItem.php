<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class MenuItem extends Model
{
    use HasFactory, HasTranslations;

    public $translatable = ['label'];

    protected $fillable = [
        'label',
        'link',
        'parent_id',
        'order',
        'target',
        'location',
    ];

    protected $casts = [
        'label' => 'array',
    ];

    /**
     * Mendefinisikan relasi ke induknya (parent).
     */
    public function parent()
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    /**
     * Mendefinisikan relasi ke anak-anaknya (children).
     */
    public function children()
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('order');
    }
}
