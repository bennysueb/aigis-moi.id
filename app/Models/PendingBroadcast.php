<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PendingBroadcast extends Model
{
    use HasFactory;
    protected $fillable = ['template_id', 'status'];

    /**
     * Definisikan relasi ke model EventEmailTemplate.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(EventEmailTemplate::class, 'template_id');
    }
}