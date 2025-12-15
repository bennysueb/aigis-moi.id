<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventEmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'subject',
        'content',
        'banner_path',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
