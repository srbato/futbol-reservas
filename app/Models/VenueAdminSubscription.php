<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VenueAdminSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'plan_slug',
        'billing_cycle',
        'status',
        'monthly_price',
        'currency',
        'payment_provider',
        'payment_external_id',
        'payment_status',
        'mp_preference_id',
        'mp_preapproval_id',
        'mp_subscription_status',
        'starts_at',
        'expires_at',
        'reminder_sent_7d_at',
        'reminder_sent_2d_at',
        'reminder_sent_0d_at',
        'referral_code_used',
    ];

    protected $casts = [
        'monthly_price' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'reminder_sent_7d_at' => 'datetime',
        'reminder_sent_2d_at' => 'datetime',
        'reminder_sent_0d_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
