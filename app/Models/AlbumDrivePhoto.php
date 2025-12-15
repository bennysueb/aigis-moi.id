<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlbumDrivePhoto extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function album()
    {
        return $this->belongsTo(Album::class);
    }
}