<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_template_id',
        'content',
    ];

    protected $casts = [
        'content' => 'array',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(SectionTemplate::class, 'section_template_id');
    }
}
