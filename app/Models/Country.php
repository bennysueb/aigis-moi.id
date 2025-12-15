<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory;

    /**
     * Tentukan nama tabel secara eksplisit
     */
    protected $table = 'countries';

    /**
     * Matikan timestamps (created_at, updated_at)
     * karena tabel dari SQL import mungkin tidak memilikinya.
     */
    public $timestamps = false;

    /**
     * Relasi: Satu negara memiliki banyak state (provinsi)
     */
    public function states(): HasMany
    {
        return $this->hasMany(State::class);
    }
}
