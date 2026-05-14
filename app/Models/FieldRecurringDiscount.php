<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FieldRecurringDiscount extends Model
{
    use HasFactory;
    protected $fillable = [
        'field_id',
        'min_occurrences',
        'discount_percentage',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }
}
