<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialMediaType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'icon_class',
    ];

    public function items()
    {
        return $this->hasMany(SocialWallItem::class);
    }
}
