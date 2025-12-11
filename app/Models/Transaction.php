<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'payable_type',
        'payable_id',
        'amount',
        'midtrans_transaction_id',
        'snap_token',
        'payment_type',
        'status',
        'payload'
    ];

    protected $casts = [
        'payload' => 'array',
        'amount' => 'decimal:2',
    ];

    // Relasi ke User pembayar
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi Polimorfik (Bisa ke Registration atau ProductOrder)
    public function payable(): MorphTo
    {
        return $this->morphTo();
    }
}
