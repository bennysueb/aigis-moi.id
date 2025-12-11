<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoucherUsage extends Model
{
    public $timestamps = false; // Kita hanya butuh used_at custom

    protected $fillable = [
        'voucher_id',
        'user_id',
        'transaction_id',
        'discount_amount',
        'used_at'
    ];

    protected $casts = [
        'used_at' => 'datetime'
    ];
}
