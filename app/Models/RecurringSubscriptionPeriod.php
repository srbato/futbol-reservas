<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecurringSubscriptionPeriod extends Model
{
    protected $fillable = [
        'recurring_subscription_id',
        'mp_authorized_payment_id',
        'period_start',
        'period_end',
        'amount_charged',
        'status',
        'reservations_created',
    ];

    protected $casts = [
        'period_start'   => 'date',
        'period_end'     => 'date',
        'amount_charged' => 'decimal:2',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(RecurringSubscription::class, 'recurring_subscription_id');
    }
}
