<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BroadcastHistory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'registration_id',
        'event_id',
        'subject',
        'content',
    ];

    /**
     * Get the registration that owns the broadcast history.
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }
}