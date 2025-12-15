<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialWallItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'social_media_type_id',
        'embed_code',
        'is_published',
        'user_id',
    ];

    public function socialMediaType()
    {
        return $this->belongsTo(SocialMediaType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
