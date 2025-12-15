<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackSubmission extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array', // Otomatis mengubah kolom JSON 'data' menjadi array
    ];

    /**
     * Mendapatkan data form yang terkait dengan submission ini.
     */
    public function feedbackForm()
    {
        return $this->belongsTo(FeedbackForm::class);
    }

    /**
     * Mendapatkan data registrasi yang terkait dengan submission ini.
     */
    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }
}
