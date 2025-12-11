<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Transaction;


class Registration extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'name',
        'email',
        'phone_number',
        'data', // <-- 'data' tetap ada untuk field kustom
        'checked_in_at',
        'attendance_type',
        'rfid_registered_at',
        'ticket_tier_id',
        'payment_status',
        'total_price'
    ];

    protected $casts = [
        'checked_in_at' => 'datetime',
        'data' => 'array',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted(): void
    {
        static::creating(function ($registration) {
            $registration->uuid = Str::uuid();
        });
    }

    public function checkinLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CheckinLog::class);
    }

    /**
     * Get all of the broadcast histories for the registration.
     */
    public function broadcastHistories(): HasMany
    {
        return $this->hasMany(BroadcastHistory::class);
    }

    public function ticketTier(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TicketTier::class);
    }

    // Link ke Pembayaran
    public function transaction(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(Transaction::class, 'payable');
    }
}
