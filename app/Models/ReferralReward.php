<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralReward extends Model
{
    protected $fillable = [
        'referrer_user_id',
        'referred_user_id',
        'subscription_id',
        'status',
        'reward_type',
        'redeemed_at',
    ];

    protected $casts = [
        'redeemed_at' => 'datetime',
    ];

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_user_id');
    }

    public function referred()
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }

    public function subscription()
    {
        return $this->belongsTo(VenueAdminSubscription::class);
    }
}
