<?php

namespace App\Models;

use App\Models\InquirySubmission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class InquiryForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'fields',
    ];

    protected $casts = [
        'fields' => 'array', // Otomatis ubah JSON ke array PHP
    ];

    public function submissions()
    {
        return $this->hasMany(InquirySubmission::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
