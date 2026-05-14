<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldException extends Model
{
    use HasFactory;
    protected $fillable = [
        'field_id',
        'date',
        'is_closed',
        'open_time',
        'close_time',
        'note',
    ];

    protected $casts = [
        'date' => 'date',
        'is_closed' => 'boolean',
    ];

    public function field()
    {
        return $this->belongsTo(Field::class);
    }
}
