<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'logo_path',
        'banner_path',
        'phone_number',
        'address',
        'bank_name',
        'bank_account',
        'bank_holder',
        'status',
        'balance'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class); // Owner toko
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(ProductOrder::class);
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }
}
