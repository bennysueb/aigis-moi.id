<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Voucher extends Model
{
    protected $fillable = [
        'code',
        'event_id', // Nullable (Global Voucher)
        'type', // percentage / fixed_amount
        'amount',
        'usage_limit',
        'usage_per_user',
        'min_purchase_amount',
        'valid_from',
        'valid_until',
        'is_active'
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'is_active' => 'boolean',
        'amount' => 'decimal:2'
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function usages(): HasMany
    {
        return $this->hasMany(VoucherUsage::class);
    }

    // Helper untuk cek validitas sederhana
    public function isValidForUser($userId)
    {
        if (!$this->is_active) return false;
        if ($this->valid_until && now()->greaterThan($this->valid_until)) return false;
        if ($this->usage_limit > 0 && $this->usages()->count() >= $this->usage_limit) return false;

        // Cek limit per user
        $userUsage = $this->usages()->where('user_id', $userId)->count();
        if ($userUsage >= $this->usage_per_user) return false;

        return true;
    }
}
