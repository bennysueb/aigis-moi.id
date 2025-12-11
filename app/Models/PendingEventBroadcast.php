<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingEventBroadcast extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'template_id',
        'status',
        'progress',
        'total_recipients',
        'error_message',
    ];

    public function template()
    {
        return $this->belongsTo(EventEmailTemplate::class, 'template_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
