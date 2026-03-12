<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FieldPrice extends Model
{
    protected $fillable = ['field_id', 'price_per_slot', 'currency'];

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }
}