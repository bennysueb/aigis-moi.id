<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Invitation extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'is_sent_email' => 'boolean',
        'is_sent_whatsapp' => 'boolean',
        'email_sent_at' => 'datetime',
        'whatsapp_sent_at' => 'datetime',
        'responded_at' => 'datetime',
        'representative_data' => 'array', // Cast JSON ke Array otomatis
    ];

    // Otomatis generate UUID saat data dibuat
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
