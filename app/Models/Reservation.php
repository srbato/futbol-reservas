<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    protected $fillable = [
        'field_id',
        'user_id',
        'start_at',
        'end_at',
        'status',
        'total_amount',
        'currency',
        'verification_code',
        'expires_at',

        'payment_provider',
        'payment_external_id',
        'payment_status',
        'mp_preference_id',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}