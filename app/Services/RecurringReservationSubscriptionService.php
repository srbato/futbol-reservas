<?php

namespace App\Services;

use App\Mail\RecurringSubscriptionActivatedMail;
use App\Mail\RecurringSubscriptionCancelledMail;
use App\Mail\RecurringSubscriptionPaymentFailedMail;
use App\Mail\VenueAdminRecurringSubscriptionActivatedMail;
use App\Models\Field;
use App\Models\Reservation;
use App\Models\RecurringSubscription;
use App\Models\RecurringSubscriptionPeriod;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RecurringReservationSubscriptionService
{
    public function __construct(
        private readonly ReservationService $reservationService,
    ) {}

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Devuelve el access token correcto y el owner label para una suscripción.
     * Requiere que field.venue esté cargado antes de llamar.
     */
    private function resolveToken(RecurringSubscription $subscription): array
    {
        $venueToken = $subscription->field->venue->mp_access_token ?? null;

        if ($venueToken) {
            return [$venueToken, 'venue'];
        }

        return [config('services.mercadopago.access_token'), 'platform'];
    }

    /**
     * Instancia Http con el token indicado y el verify correcto según entorno.
     */
    private function http(string $accessToken)
    {
        return Http::withOptions(['verify' => app()->isProduction()])
            ->withToken($accessToken);
    }

    // -------------------------------------------------------------------------
    // Cálculo de monto mensual
    // -------------------------------------------------------------------------

    public function calculateMonthlyAmount(Field $field, string $frequency): float
    {
        $field->loadMissing('price');

        $slotsPerMonth = $frequency === 'biweekly' ? 2 : 4;
        $pricePerSlot  = (float) ($field->price?->price_per_slot ?? 0);

        return round($pricePerSlot * $slotsPerMonth, 2);
    }

    // -------------------------------------------------------------------------
    // Crear preapproval en Mercado Pago
    // -------------------------------------------------------------------------

    public function createPreapproval(RecurringSubscription $subscription): array
    {
        $subscription->loadMissing(['field.venue', 'user']);

        $field = $subscription->field;
        $venue = $field->venue;

        [$accessToken, $tokenOwner] = $this->resolveToken($subscription);

        $baseUrl = rtrim(config('app.url'), '/');

        $slotsPerMonth = $subscription->frequency === 'biweekly' ? 2 : 4;
        $totalMonths   = max(1, ceil(($subscription->occurrences ?? $slotsPerMonth) / $slotsPerMonth));
        $startDate     = now()->format('Y-m-d\TH:i:s.000-03:00');
        $endDate       = now()->addMonths($totalMonths)->format('Y-m-d\TH:i:s.000-03:00');

        $payload = [
            'reason'             => "Turno {$subscription->dayLabel()} {$subscription->start_time} - {$field->name} ({$venue->name})",
            'payer_email'        => $subscription->user->email,
            'auto_recurring'     => [
                'frequency'          => 1,
                'frequency_type'     => 'months',
                'transaction_amount' => (float) $subscription->monthly_amount,
                'currency_id'        => $subscription->currency,
                'start_date'         => $startDate,
                'end_date'           => $endDate,
            ],
            'back_url'           => $baseUrl . '/reservas/suscripcion/' . $subscription->id . '/resultado',
            'notification_url'   => $baseUrl . '/webhooks/mercadopago',
            'external_reference' => 'recurring_subscription:' . $subscription->id,
            'status'             => 'pending',
        ];

        $response = $this->http($accessToken)->post('https://api.mercadopago.com/preapproval', $payload);

        if ($response->successful()) {
            $data = $response->json();
            $subscription->mp_preapproval_id        = $data['id'] ?? null;
            $subscription->payment_mp_token_owner   = $tokenOwner;
            $subscription->save();
        } else {
            Log::error('RecurringSubscription: error al crear preapproval', [
                'subscription_id' => $subscription->id,
                'status'          => $response->status(),
                'body'            => $response->body(),
            ]);
        }

        return [$response->successful(), $response->json()];
    }

    // -------------------------------------------------------------------------
    // Consultar estado del preapproval en MP (fallback si el webhook no llega)
    // -------------------------------------------------------------------------

    public function syncPreapprovalStatus(RecurringSubscription $subscription): void
    {
        $subscription->loadMissing(['field.venue']);

        [$accessToken] = $this->resolveToken($subscription);

        $response = $this->http($accessToken)
            ->get("https://api.mercadopago.com/preapproval/{$subscription->mp_preapproval_id}");

        if (!$response->successful()) {
            Log::warning('RecurringSubscription: no se pudo consultar preapproval en MP', [
                'subscription_id' => $subscription->id,
                'status'          => $response->status(),
            ]);
            return;
        }

        $data = $response->json();
        $mpStatus = $data['status'] ?? null;

        if ($mpStatus === 'authorized' && $subscription->status === 'PENDING_PAYMENT') {
            $this->activateSubscription($subscription, $data);
        } elseif (in_array($mpStatus, ['cancelled', 'paused'])) {
            $subscription->status = 'CANCELLED';
            $subscription->cancelled_at = now();
            $subscription->cancel_reason = 'mp_' . $mpStatus;
            $subscription->save();
        }
    }

    // -------------------------------------------------------------------------
    // Activar suscripción cuando MP autoriza el preapproval
    // -------------------------------------------------------------------------

    public function activateSubscription(RecurringSubscription $subscription, array $preapprovalData): void
    {
        // Evitar doble activación
        if ($subscription->status === 'ACTIVE') {
            return;
        }

        $subscription->loadMissing(['field.venue', 'field.schedules', 'field.exceptions', 'field.price', 'user']);

        $firstOccurrence = $subscription->nextOccurrences(1, now())[0];

        $startsAt = $firstOccurrence->toDateString();

        $subscription->status                  = 'ACTIVE';
        $subscription->mp_subscription_status  = $preapprovalData['status'] ?? 'authorized';
        $subscription->subscription_starts_at  = $startsAt;
        $subscription->next_billing_date       = $startsAt;
        $subscription->save();

        // Crear las reservas del primer mes inmediatamente
        try {
            $this->createFirstMonthReservations($subscription);
        } catch (\Throwable $e) {
            Log::error('RecurringSubscription: error creando reservas del primer mes', [
                'subscription_id' => $subscription->id,
                'error'           => $e->getMessage(),
            ]);
        }

        try {
            Mail::to($subscription->user->email)
                ->queue(new RecurringSubscriptionActivatedMail($subscription));

            $owner = $subscription->field->venue->owner;
            if ($owner) {
                Mail::to($owner->email)
                    ->queue(new VenueAdminRecurringSubscriptionActivatedMail($subscription));
            }
        } catch (\Exception $e) {
            Log::error('RecurringSubscription: error enviando mail de activación', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Crea las reservas del primer mes al activar la suscripción.
     * Esto asegura que las reservas existan sin depender del webhook de authorized_payment.
     */
    private function createFirstMonthReservations(RecurringSubscription $subscription): void
    {
        // Si ya tiene reservas creadas, no duplicar
        if ($subscription->reservations()->exists()) {
            return;
        }

        $subscription->loadMissing([
            'field.venue', 'field.schedules', 'field.exceptions', 'field.price',
        ]);

        $slotsPerMonth = $subscription->frequency === 'biweekly' ? 2 : 4;
        $dates = $subscription->nextOccurrences($slotsPerMonth, now());

        $field = $subscription->field;
        $count = 0;

        foreach ($dates as $slotCarbon) {
            try {
                $this->reservationService->createSingle(
                    field: $field,
                    start: $slotCarbon,
                    userId: $subscription->user_id,
                    expiresInMinutes: null,
                    batchId: null,
                    recurringSubscriptionId: $subscription->id,
                );
                $count++;
            } catch (\Throwable $e) {
                Log::warning('RecurringSubscription: slot no disponible al activar', [
                    'subscription_id' => $subscription->id,
                    'slot'            => $slotCarbon->toDateTimeString(),
                    'error'           => $e->getMessage(),
                ]);
            }
        }

        Log::info('RecurringSubscription: reservas del primer mes creadas', [
            'subscription_id' => $subscription->id,
            'count'           => $count,
            'total_expected'  => $slotsPerMonth,
        ]);
    }

    // -------------------------------------------------------------------------
    // Generar reservas del mes cuando MP cobra
    // -------------------------------------------------------------------------

    public function generateMonthReservations(
        RecurringSubscription $subscription,
        string $mpAuthorizedPaymentId,
        float $amountCharged,
    ): RecurringSubscriptionPeriod {
        // Idempotencia: si ya procesamos este pago, devolver el período existente
        $existing = RecurringSubscriptionPeriod::where('mp_authorized_payment_id', $mpAuthorizedPaymentId)->first();
        if ($existing) {
            return $existing;
        }

        // Determinar el rango del período
        $periodStart = $subscription->last_payment_at
            ? $subscription->last_payment_at->copy()
            : $subscription->subscription_starts_at->copy();

        $slotsPerMonth = $subscription->frequency === 'biweekly' ? 2 : 4;

        // Generar los turnos correspondientes a un mes de suscripción
        $datesInPeriod = $subscription->nextOccurrences($slotsPerMonth, $periodStart);

        $periodEnd = !empty($datesInPeriod)
            ? end($datesInPeriod)->copy()->addDay()
            : $periodStart->copy()->addDays(30);

        $periodStartStr = $periodStart->toDateString();
        $periodEndStr   = $periodEnd->toDateString();

        // Crear el período con estado inicial
        $period = RecurringSubscriptionPeriod::create([
            'recurring_subscription_id' => $subscription->id,
            'mp_authorized_payment_id'  => $mpAuthorizedPaymentId,
            'period_start'              => $periodStartStr,
            'period_end'                => $periodEndStr,
            'amount_charged'            => $amountCharged,
            'status'                    => 'PAID',
            'reservations_created'      => 0,
        ]);

        // Cargar relaciones necesarias para crear reservas
        $subscription->loadMissing([
            'field.venue',
            'field.schedules',
            'field.exceptions',
            'field.price',
        ]);

        $field = $subscription->field;
        $count = 0;

        foreach ($datesInPeriod as $slotCarbon) {
            try {
                $this->reservationService->createSingle(
                    field: $field,
                    start: $slotCarbon,
                    userId: $subscription->user_id,
                    expiresInMinutes: null,
                    batchId: null,
                    recurringSubscriptionId: $subscription->id,
                );
                $count++;
            } catch (\Throwable $e) {
                Log::warning('RecurringSubscription: slot no disponible', [
                    'subscription_id' => $subscription->id,
                    'slot'            => $slotCarbon->toDateTimeString(),
                    'error'           => $e->getMessage(),
                ]);
            }
        }

        $period->reservations_created = $count;
        $period->save();

        $subscription->last_payment_id   = $mpAuthorizedPaymentId;
        $subscription->last_payment_at   = now();
        $subscription->next_billing_date = Carbon::now()->addDays(30)->toDateString();
        $subscription->save();

        return $period;
    }

    // -------------------------------------------------------------------------
    // Manejar pago fallido
    // -------------------------------------------------------------------------

    public function handleFailedPayment(
        RecurringSubscription $subscription,
        string $mpAuthorizedPaymentId,
        float $amountCharged,
    ): void {
        // Idempotencia
        $existing = RecurringSubscriptionPeriod::where('mp_authorized_payment_id', $mpAuthorizedPaymentId)->first();
        if ($existing) {
            return;
        }

        $periodStartStr = $subscription->last_payment_at
            ? $subscription->last_payment_at->toDateString()
            : $subscription->subscription_starts_at->toDateString();

        RecurringSubscriptionPeriod::create([
            'recurring_subscription_id' => $subscription->id,
            'mp_authorized_payment_id'  => $mpAuthorizedPaymentId,
            'period_start'              => $periodStartStr,
            'period_end'                => Carbon::parse($periodStartStr)->addDays(30)->toDateString(),
            'amount_charged'            => $amountCharged,
            'status'                    => 'FAILED',
            'reservations_created'      => 0,
        ]);

        $subscription->status = 'FAILED';
        $subscription->save();

        Log::error('RecurringSubscription: pago fallido', [
            'subscription_id'          => $subscription->id,
            'mp_authorized_payment_id' => $mpAuthorizedPaymentId,
            'amount_charged'           => $amountCharged,
        ]);

        try {
            Mail::to($subscription->user->email)
                ->queue(new RecurringSubscriptionPaymentFailedMail($subscription));
        } catch (\Exception $e) {
            Log::error('RecurringSubscription: error enviando mail de pago fallido', ['error' => $e->getMessage()]);
        }
    }

    // -------------------------------------------------------------------------
    // Cancelar suscripción
    // -------------------------------------------------------------------------

    public function cancelSubscription(RecurringSubscription $subscription, string $reason = 'user_requested'): bool
    {
        $subscription->loadMissing('field.venue');

        [$accessToken] = $this->resolveToken($subscription);

        $mpSuccess = false;

        if ($subscription->mp_preapproval_id) {
            $response = $this->http($accessToken)->put(
                "https://api.mercadopago.com/preapproval/{$subscription->mp_preapproval_id}",
                ['status' => 'cancelled'],
            );

            $mpSuccess = $response->successful();

            if (!$mpSuccess) {
                Log::error('RecurringSubscription: error al cancelar preapproval en MP', [
                    'subscription_id' => $subscription->id,
                    'preapproval_id'  => $subscription->mp_preapproval_id,
                    'status'          => $response->status(),
                    'body'            => $response->body(),
                ]);
            }
        }

        // Actualizar estado local independientemente de si MP respondió OK
        $subscription->status      = 'CANCELLED';
        $subscription->cancelled_at = now();
        $subscription->cancel_reason = $reason;
        $subscription->save();

        // Cancelar reservas futuras asociadas a esta suscripción
        Reservation::where('recurring_subscription_id', $subscription->id)
            ->where('start_at', '>', now())
            ->whereIn('status', ['PAID', 'PENDING_PAYMENT'])
            ->update(['status' => 'CANCELLED']);

        try {
            Mail::to($subscription->user->email)
                ->queue(new RecurringSubscriptionCancelledMail($subscription));
        } catch (\Exception $e) {
            Log::error('RecurringSubscription: error enviando mail de cancelación', ['error' => $e->getMessage()]);
        }

        return $mpSuccess;
    }
}
