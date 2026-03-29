<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    protected $fillable = [
        'batch_id',
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
        'payment_mp_token_owner',
        'reminder_sent',
        'referral_reward_id',
        'match_result',
        'match_outcome',
        'notes',
        'modified_at',
        'modification_status',
        'modify_mp_preference_id',
        'modify_diff_amount',
        'modify_field_id',
        'modify_start_at',
    ];

    protected $casts = [
        'start_at'      => 'datetime',
        'end_at'        => 'datetime',
        'expires_at'    => 'datetime',
        'modified_at'        => 'datetime',
        'modify_start_at'    => 'datetime',
        'modify_diff_amount' => 'float',
        'reminder_sent'      => 'boolean',
    ];

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(ReservationBatch::class, 'batch_id');
    }

    public function players(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ReservationPlayer::class)->with('user');
    }

    public function results(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ReservationResult::class);
    }
}