<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    // Menentukan nama tabel secara eksplisit
    protected $table = 'settings';

    // Memberitahu Laravel bahwa 'key' adalah primary key
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';

    // Kolom yang bisa diisi
    protected $fillable = ['key', 'value'];

    // Menonaktifkan timestamps (created_at, updated_at) jika tidak ada di tabel Anda
    public $timestamps = false;
}
