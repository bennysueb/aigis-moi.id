<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollaboratorCategory extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi ke Collaborators
    public function collaborators()
    {
        return $this->hasMany(Collaborator::class)->orderBy('sort_order');
    }
}
