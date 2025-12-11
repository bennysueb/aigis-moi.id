<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'welcome_section_id',
        'item_id',
        'item_type',
        'order',
    ];

    /**
     * Get the parent item model (Event or Post).
     */
    public function item()
    {
        return $this->morphTo();
    }
}
