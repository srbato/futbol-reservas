<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FieldPrice extends Model
{
    protected $fillable = [
        'field_id',
        'price_per_slot',
        'currency',
        'night_price_per_slot',
        'night_start_time',
        'night_end_time',
    ];

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }
}