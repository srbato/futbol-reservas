<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class RecurringSubscription extends Model
{
    protected $fillable = [
        'user_id', 'field_id', 'status', 'frequency', 'occurrences', 'day_of_week',
        'start_time', 'slot_minutes', 'monthly_amount', 'currency',
        'mp_preapproval_id', 'mp_subscription_status', 'payment_mp_token_owner',
        'next_billing_date', 'subscription_starts_at', 'cancelled_at',
        'cancel_reason', 'last_payment_id', 'last_payment_at',
    ];

    protected $casts = [
        'monthly_amount'         => 'decimal:2',
        'next_billing_date'      => 'date',
        'subscription_starts_at' => 'date',
        'cancelled_at'           => 'datetime',
        'last_payment_at'        => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function periods(): HasMany
    {
        return $this->hasMany(RecurringSubscriptionPeriod::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'ACTIVE';
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            'PENDING_PAYMENT' => 'Pendiente de autorización',
            'ACTIVE'          => 'Activa',
            'PAUSED'          => 'Pausada',
            'CANCELLED'       => 'Cancelada',
            'FAILED'          => 'Pago fallido',
            default           => $this->status,
        };
    }

    public function dayLabel(): string
    {
        $days = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        return $days[$this->day_of_week] ?? '';
    }

    public function frequencyLabel(): string
    {
        return $this->frequency === 'weekly' ? 'Semanal' : 'Quincenal';
    }

    /**
     * Genera las próximas N fechas de turno a partir de hoy (o desde $from).
     * Respeta day_of_week, start_time y frequency.
     */
    public function nextOccurrences(int $count = 4, ?Carbon $from = null): array
    {
        $from  = $from ?? now();
        $dates = [];
        $cursor = $from->copy()->startOfDay();

        // Avanzar hasta el primer día de la semana correcto
        while ($cursor->dayOfWeek !== $this->day_of_week) {
            $cursor->addDay();
        }

        $weekInterval = $this->frequency === 'biweekly' ? 2 : 1;

        while (count($dates) < $count) {
            $dt = $cursor->copy()->setTimeFromTimeString($this->start_time);
            if ($dt->isAfter($from)) {
                $dates[] = $dt;
            }
            $cursor->addWeeks($weekInterval);
        }

        return $dates;
    }
}
