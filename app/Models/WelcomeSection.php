<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class WelcomeSection extends Model
{
    use HasFactory;
    use HasTranslations;

    protected $fillable = [
        'name',
        'component',
        'order',
        'is_visible',
        'custom_section_id'
    ];

    public $translatable = ['name'];

    protected $casts = [
        'name' => 'array',
        'is_visible' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(SectionItem::class)->orderBy('order');
    }

    public function customSection()
    {
        return $this->belongsTo(CustomSection::class, 'custom_section_id');
    }
}
