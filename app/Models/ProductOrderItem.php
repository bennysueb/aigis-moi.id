<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductOrderItem extends Model
{
    protected $fillable = [
        'product_order_id',
        'product_id',
        'quantity',
        'price_at_purchase',
        'subtotal'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
