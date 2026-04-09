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
        'long_term_months',
        'trial_ends_at',
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

    protected $hidden = [
        'payment_external_id',
        'mp_preference_id',
        'mp_preapproval_id',
    ];

    protected $casts = [
        'monthly_price' => 'decimal:2',
        'starts_at'     => 'datetime',
        'expires_at'    => 'datetime',
        'trial_ends_at' => 'datetime',
        'reminder_sent_7d_at' => 'datetime',
        'reminder_sent_2d_at' => 'datetime',
        'reminder_sent_0d_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'ACTIVE'          => 'Activa',
            'TRIAL'           => 'Período de prueba',
            'PENDING_PAYMENT' => 'Pendiente de pago',
            'CANCELLED'       => 'Cancelada',
            'EXPIRED'         => 'Expirada',
            default           => $this->status ?? '-',
        };
    }

    public function statusStyles(): string
    {
        return match ($this->status) {
            'ACTIVE'          => 'background:#e8f7ee; color:#157347; border:1px solid #cfe9d7;',
            'TRIAL'           => 'background:#e8f0ff; color:#5b21b6; border:1px solid #c4b5fd;',
            'PENDING_PAYMENT' => 'background:#fff4db; color:#9a6700; border:1px solid #f5d48a;',
            'CANCELLED'       => 'background:#f8d7da; color:#842029; border:1px solid #f1b9c0;',
            'EXPIRED'         => 'background:#f8d7da; color:#842029; border:1px solid #f1b9c0;',
            default           => 'background:#f3f3f3; color:#444; border:1px solid #e2e2e2;',
        };
    }
}
