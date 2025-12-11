<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class ProductOrder extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'tenant_id',
        'total_amount',
        'discount_amount',
        'admin_fee',
        'final_amount',
        'status',
        'shipping_address',
        'tracking_number',
        'cancellation_reason'
    ];

    // Item belanjaan
    public function items(): HasMany
    {
        return $this->hasMany(ProductOrderItem::class);
    }

    // Link ke Pembayaran (Order ini adalah "payable"-nya)
    public function transaction(): MorphOne
    {
        return $this->morphOne(Transaction::class, 'payable');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class); // Pembeli
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class); // Penjual
    }
}
