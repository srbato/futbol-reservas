<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FaltaUnoSetting extends Model
{
    protected $fillable = [
        'field_id',
        'enabled',
        'refund_deadline_minutes',
        'fill_deadline_minutes',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }
}
