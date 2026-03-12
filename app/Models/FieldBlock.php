<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FieldBlock extends Model
{
    protected $fillable = [
        'field_id',
        'date',
        'start_time',
        'end_time',
        'reason',
    ];

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }
}
