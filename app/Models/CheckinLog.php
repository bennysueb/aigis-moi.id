<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheckinLog extends Model
{
    use HasFactory;

    // Kolom yang boleh diisi secara massal
    protected $fillable = [
        'registration_id',
        'checkin_time',
    ];

    // Tabel ini tidak menggunakan kolom created_at dan updated_at bawaan
    public $timestamps = false;

    /**
     * Mendapatkan data registrasi yang memiliki log ini.
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }
}
