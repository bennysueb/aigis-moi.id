<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends Model
{
    use HasFactory;

    /**
     * Tentukan nama tabel secara eksplisit
     */
    protected $table = 'states';

    /**
     * Matikan timestamps
     */
    public $timestamps = false;

    /**
     * Relasi: Satu state (provinsi) dimiliki oleh satu negara
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Relasi: Satu state (provinsi) memiliki banyak kota
     */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}
