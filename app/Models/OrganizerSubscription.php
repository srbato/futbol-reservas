<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizerSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'plan',
        'status',
        'monthly_price',
        'currency',
        'mp_preapproval_id',
        'mp_subscription_status',
        'trial_ends_at',
        'starts_at',
        'expires_at',
        'cancelled_at',
    ];

    protected $hidden = [
        'mp_preapproval_id',
    ];

    protected $casts = [
        'monthly_price'  => 'decimal:2',
        'trial_ends_at'  => 'datetime',
        'starts_at'      => 'datetime',
        'expires_at'     => 'datetime',
        'cancelled_at'   => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'ACTIVE'          => 'Activa',
            'TRIAL'           => 'Trial',
            'PENDING_PAYMENT' => 'Pendiente',
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

    public function isActive(): bool
    {
        return in_array($this->status, ['ACTIVE', 'TRIAL']);
    }

    public function isPro(): bool
    {
        return $this->plan === 'pro' && $this->isActive();
    }
}
