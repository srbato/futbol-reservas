<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReservationBatch extends Model
{
    protected $fillable = [
        'user_id',
        'field_id',
        'subtotal',
        'discount_percentage',
        'discount_amount',
        'total_amount',
        'currency',
        'status',
        'payment_provider',
        'mp_preference_id',
        'payment_external_id',
        'payment_status',
        'payment_mp_token_owner',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'batch_id');
    }
}
