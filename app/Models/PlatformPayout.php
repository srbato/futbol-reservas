<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlatformPayout extends Model
{
    protected $fillable = [
        'venue_id',
        'reservation_id',
        'referral_reward_id',
        'amount',
        'currency',
        'status',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount'  => 'float',
    ];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function referralReward(): BelongsTo
    {
        return $this->belongsTo(ReferralReward::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
