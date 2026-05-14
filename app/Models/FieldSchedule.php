<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FieldSchedule extends Model
{
    use HasFactory;
    protected $fillable = [
        'field_id',
        'day_of_week',
        'open_time',
        'close_time'
    ];

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }
}
