<?php

namespace App\Http\Controllers;

use App\Mail\ReservationModifiedMail;
use App\Mail\ReservationPaidMail;
use App\Notifications\ReservationConfirmedNotification;
use App\Models\Field;
use App\Models\PlatformPayout;
use App\Models\Reservation;
use App\Models\ReservationBatch;
use App\Models\RecurringSubscription;
use App\Models\RecurringSubscriptionPeriod;
use App\Models\Venue;
use App\Models\VenueAdminSubscription;
use App\Services\MercadoPagoSubscriptionService;
use App\Services\RecurringReservationSubscriptionService;
use App\Services\ReservationModifyService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MercadoPagoWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Verificación de firma HMAC según documentación oficial de MercadoPago Webhooks v2
        $secret = config('services.mercadopago.webhook_secret');

        // Si el secret no está configurado, rechazar en producción por seguridad.
        // Para configurar: panel de MP > Mi negocio > Configuración > Webhooks > Editar > Secreto del webhook
        if (empty($secret)) {
            if (app()->environment('production')) {
                Log::error('MP webhook: MERCADOPAGO_WEBHOOK_SECRET no configurado en producción. Rechazando request.');
                return response()->json(['error' => 'Webhook secret not configured'], 503);
            }
            Log::warning('MP webhook: MERCADOPAGO_WEBHOOK_SECRET no configurado, omitiendo verificación de firma (entorno no productivo).');
        }

        $xSignature = $request->header('x-signature', '');
        $xRequestId = $request->header('x-request-id', '');
        $dataId     = $request->input('data.id') ?? $request->input('id');

        // Parsear ts y v1 del header x-signature (formato: ts=TIMESTAMP,v1=HASH)
        $ts = null;
        $v1 = null;
        foreach (explode(',', $xSignature) as $part) {
            [$key, $value] = array_pad(explode('=', $part, 2), 2, '');
            if ($key === 'ts') {
                $ts = $value;
            } elseif ($key === 'v1') {
                $v1 = $value;
            }
        }

        if (!empty($secret)) {
            if (!$ts || !$v1) {
                Log::warning('MP webhook: header x-signature inválido o ausente');
                return response()->json(['error' => 'Invalid signature'], 401);
            }

            $manifest = "id:{$dataId};request-id:{$xRequestId};ts:{$ts};";
            $computed = hash_hmac('sha256', $manifest, $secret);

            if (!hash_equals($computed, $v1)) {
                Log::warning('MP webhook: firma HMAC no coincide', [
                    'manifest' => $manifest,
                ]);
                return response()->json(['error' => 'Invalid signature'], 401);
            }
        }

        Log::info('MP webhook recibido', [
            'type'    => $request->input('type'),
            'topic'   => $request->input('topic'),
            'data_id' => $request->input('data.id') ?? $request->input('id'),
        ]);

        $type   = $request->input('type');
        $topic  = $request->input('topic');
        $dataId = $request->input('data.id') ?? $request->input('id');

        if ($topic === 'merchant_order') {
            return response()->json(['ok' => true]);
        }

        // Handle subscription preapproval status change
        if ($type === 'subscription_preapproval' && $dataId) {
            return $this->handleSubscriptionPreapproval($dataId);
        }

        // Handle subscription automatic payment
        if ($type === 'subscription_authorized_payment' && $dataId) {
            return $this->handleSubscriptionAuthorizedPayment($dataId);
        }

        if ($type !== 'payment' || !$dataId) {
            return response()->json(['ok' => true]);
        }

        // Se usa el token de plataforma porque en este punto aún no sabemos a qué venue
        // pertenece el pago. Luego, en handleReservationPayment/handleBatchPayment se
        // re-consulta con el token específico del venue para mayor seguridad.
        $paymentResponse = Http::withOptions(['verify' => app()->isProduction()])
            ->withToken(config('services.mercadopago.access_token'))
            ->get("https://api.mercadopago.com/v1/payments/{$dataId}");

        if (!$paymentResponse->successful()) {
            Log::error('No se pudo consultar el pago en MP', [
                'payment_id' => $dataId,
                'response'   => $paymentResponse->body(),
            ]);
            return response()->json(['ok' => false], 500);
        }

        $payment = $paymentResponse->json();

        $externalReference = $payment['external_reference'] ?? null;
        $paymentStatus     = $payment['status'] ?? null;
        $paymentId         = $payment['id'] ?? null;

        if (!$externalReference) {
            Log::warning('Pago sin external_reference', $payment);
            return response()->json(['ok' => true]);
        }

        if (str_starts_with($externalReference, 'modify:')) {
            return $this->handleModifyPayment($externalReference, $paymentStatus, $paymentId);
        }

        if (str_starts_with($externalReference, 'venue_admin_subscription:')) {
            return $this->handleVenueAdminSubscriptionPayment(
                $externalReference,
                $paymentStatus,
                $paymentId
            );
        }

        if (str_starts_with($externalReference, 'batch:')) {
            return $this->handleBatchPayment(
                $externalReference,
                $paymentStatus,
                $paymentId
            );
        }

        if (str_starts_with($externalReference, 'platform_payout:venue:')) {
            return $this->handlePlatformPayoutPayment(
                $externalReference,
                $paymentStatus,
                $paymentId
            );
        }

        return $this->handleReservationPayment(
            $externalReference,
            $paymentStatus,
            $paymentId
        );
    }

    private function handleSubscriptionPreapproval(string $preapprovalId)
    {
        $service = app(MercadoPagoSubscriptionService::class);
        [$ok, $data] = $service->getPreapproval($preapprovalId);

        // Si el token de plataforma no puede obtener el preapproval,
        // intentar con el token del venue (recurring subscriptions usan el token del venue)
        if (!$ok) {
            $recurring = RecurringSubscription::where('mp_preapproval_id', $preapprovalId)
                ->with('field.venue')
                ->first();

            if ($recurring) {
                $venueToken = $recurring->field->venue->mp_access_token ?? null;
                if ($venueToken) {
                    $response = Http::withOptions(['verify' => app()->isProduction()])
                        ->withToken($venueToken)
                        ->get("https://api.mercadopago.com/preapproval/{$preapprovalId}");

                    if ($response->successful()) {
                        return $this->handleRecurringSubscriptionPreapproval($preapprovalId, $response->json());
                    }
                }
            }

            Log::error('MP Subscription: no se pudo obtener preapproval', ['preapproval_id' => $preapprovalId]);
            return response()->json(['ok' => true]);
        }

        $externalReference = $data['external_reference'] ?? null;
        $mpStatus          = $data['status'] ?? null;

        // Find subscription: first by preapproval id, then by external_reference
        $subscription = VenueAdminSubscription::where('mp_preapproval_id', $preapprovalId)->first();

        if (!$subscription && str_starts_with((string) $externalReference, 'venue_admin_subscription:')) {
            $subscriptionId = str_replace('venue_admin_subscription:', '', $externalReference);
            $subscription   = VenueAdminSubscription::with('user')->find($subscriptionId);
        }

        if (!$subscription) {
            // Intentar como RecurringSubscription antes de loguear el warning
            if (str_starts_with((string) $externalReference, 'recurring_subscription:')) {
                return $this->handleRecurringSubscriptionPreapproval($preapprovalId, $data);
            }

            Log::warning('MP Subscription: suscripción no encontrada para preapproval', [
                'preapproval_id'     => $preapprovalId,
                'external_reference' => $externalReference,
            ]);
            return response()->json(['ok' => true]);
        }

        $subscription->loadMissing('user');

        if (!$subscription->mp_preapproval_id) {
            $subscription->mp_preapproval_id = $preapprovalId;
        }

        $subscription->mp_subscription_status = $mpStatus;

        $wasActiveBefore = $subscription->status === 'ACTIVE';

        if ($mpStatus === 'authorized') {
            if ($subscription->status === 'PENDING_PAYMENT') {
                $user          = $subscription->user;
                $currentActive = $user?->activeVenueAdminSubscription()->first();

                if ($currentActive && $currentActive->id !== $subscription->id && $currentActive->expires_at?->isFuture()) {
                    $startsAt = $currentActive->expires_at->copy();
                } else {
                    $startsAt = now();
                }

                $billingDays = $subscription->billing_cycle === 'annual' ? ($subscription->long_term_months * 30) : 30;
                $trialDays   = $subscription->trial_ends_at ? (int) now()->diffInDays($subscription->trial_ends_at, false) : 0;
                $trialDays   = max(0, $trialDays);

                $subscription->status     = 'ACTIVE';
                $subscription->starts_at  = $startsAt;
                $subscription->expires_at = $startsAt->copy()->addDays($trialDays + $billingDays);

                if ($user && $user->role === 'user') {
                    $user->role = 'venue_admin';
                    $user->save();
                }
            }
        } elseif ($mpStatus === 'cancelled' && $subscription->status === 'PENDING_PAYMENT') {
            $subscription->status = 'CANCELLED';
        }

        $subscription->save();

        if ($mpStatus === 'authorized' && !$wasActiveBefore) {
            app(\App\Services\ReferralService::class)->processReward($subscription);

            Log::info('Suscripción activada via preapproval', [
                'subscription_id' => $subscription->id,
                'preapproval_id'  => $preapprovalId,
            ]);
        }

        return response()->json(['ok' => true]);
    }

    private function handleSubscriptionAuthorizedPayment(string $paymentId)
    {
        $service = app(MercadoPagoSubscriptionService::class);
        [$ok, $data] = $service->getAuthorizedPayment($paymentId);

        // Si el token de plataforma no puede obtener el pago,
        // buscar la RecurringSubscription y reintentar con el token del venue
        if (!$ok) {
            $data = $this->fetchAuthorizedPaymentWithVenueToken($paymentId);
            if (!$data) {
                Log::error('MP Subscription: no se pudo obtener authorized_payment', ['payment_id' => $paymentId]);
                return response()->json(['ok' => true]);
            }
            $ok = true;
        }

        $preapprovalId = $data['preapproval_id'] ?? null;
        $status        = $data['status'] ?? null;

        if (!$preapprovalId) {
            return response()->json(['ok' => true]);
        }

        $subscription = VenueAdminSubscription::where('mp_preapproval_id', $preapprovalId)->first();

        if (!$subscription) {
            // Intentar como RecurringSubscription antes de loguear el warning
            $recurring = RecurringSubscription::where('mp_preapproval_id', $preapprovalId)->first();
            if ($recurring) {
                return $this->handleRecurringSubscriptionAuthorizedPayment($paymentId, $data);
            }

            if ($status !== 'processed') {
                return response()->json(['ok' => true]);
            }

            Log::warning('MP Subscription: suscripción no encontrada para authorized_payment', [
                'payment_id'     => $paymentId,
                'preapproval_id' => $preapprovalId,
            ]);
            return response()->json(['ok' => true]);
        }

        if ($status !== 'processed') {
            return response()->json(['ok' => true]);
        }

        // Idempotency: skip if already processed this exact payment
        if ($subscription->payment_external_id === (string) $paymentId) {
            return response()->json(['ok' => true]);
        }

        $days      = $subscription->billing_cycle === 'annual' ? ($subscription->long_term_months * 30) : 30;
        $baseDate  = ($subscription->expires_at?->isFuture() ? $subscription->expires_at : now());
        $newExpiry = $baseDate->copy()->addDays($days);

        $subscription->update([
            'payment_external_id'    => (string) $paymentId,
            'payment_status'         => 'approved',
            'mp_subscription_status' => 'authorized',
            'status'                 => 'ACTIVE',
            'expires_at'             => $newExpiry,
        ]);

        Log::info('Suscripción renovada automáticamente', [
            'subscription_id' => $subscription->id,
            'payment_id'      => $paymentId,
            'new_expiry'      => $newExpiry->toDateTimeString(),
        ]);

        return response()->json(['ok' => true]);
    }

    private function handleReservationPayment(string $reservationId, ?string $paymentStatus, $paymentId)
    {
        $reservation = Reservation::find($reservationId);

        if (!$reservation) {
            Log::warning('Reserva no encontrada para webhook MP', [
                'reservation_id' => $reservationId,
            ]);
            return response()->json(['ok' => true]);
        }

        $reservation->loadMissing('field.venue');

        if (!$reservation->field || !$reservation->field->venue) {
            Log::error('Webhook reserva: field o venue eliminado', [
                'reservation_id' => $reservation->id,
                'field_id' => $reservation->field_id,
            ]);
            return response()->json(['ok' => true]);
        }

        $accessToken = $reservation->field->venue->mp_access_token
            ?? config('services.mercadopago.access_token');

        $paymentResponse = Http::withOptions(['verify' => app()->isProduction()])
            ->withToken($accessToken)
            ->get("https://api.mercadopago.com/v1/payments/{$paymentId}");

        if (!$paymentResponse->successful()) {
            Log::error('No se pudo re-consultar el pago con token del venue', [
                'payment_id' => $paymentId,
                'venue_id'   => $reservation->field->venue->id,
            ]);
        }

        if ($reservation->payment_external_id === (string) $paymentId && $reservation->status === 'PAID') {
            Log::info('Webhook reserva: ya procesado, ignorando', ['payment_id' => $paymentId, 'reservation_id' => $reservation->id]);
            return response()->json(['ok' => true]);
        }

        if ($paymentResponse->successful()) {
            $paidAmount     = (float) ($paymentResponse->json()['transaction_amount'] ?? 0);
            $expectedAmount = (float) $reservation->total_amount;
            if ($expectedAmount > 0 && abs($paidAmount - $expectedAmount) > 1) {
                Log::warning('Webhook reserva: discrepancia de monto', [
                    'reservation_id'  => $reservation->id,
                    'expected_amount' => $expectedAmount,
                    'paid_amount'     => $paidAmount,
                    'payment_id'      => $paymentId,
                ]);
            }
        }

        // Verificar si la reserva ya fue cancelada o expiró antes de marcar como pagada
        if (in_array($reservation->status, ['EXPIRED', 'CANCELLED'])) {
            Log::warning('Webhook reserva: pago recibido para reserva ya expirada/cancelada', [
                'reservation_id' => $reservation->id,
                'status'         => $reservation->status,
                'payment_id'     => $paymentId,
                'payment_status' => $paymentStatus,
            ]);
            return response()->json(['ok' => true]);
        }

        $reservation->payment_provider    = 'mercadopago';
        $reservation->payment_external_id = (string) $paymentId;
        $reservation->payment_status      = $paymentStatus;

        $wasPaidBefore = $reservation->status === 'PAID';

        if ($paymentStatus === 'approved') {
            $reservation->status     = 'PAID';
            $reservation->expires_at = null;
        }

        $reservation->save();

        if ($paymentStatus === 'approved' && !$wasPaidBefore) {
            $reservation->loadMissing(['user', 'field.venue.owner']);

            if ($reservation->user && $reservation->user->email) {
                Mail::to($reservation->user->email)
                    ->send(new ReservationPaidMail($reservation));

                $reservation->user->notify(new ReservationConfirmedNotification($reservation));
            }

            $venueOwner = $reservation->field->venue->owner;
            if ($venueOwner && $venueOwner->email) {
                Mail::to($venueOwner->email)
                    ->send(new \App\Mail\VenueAdminReservationMail($reservation));
            }
        }

        return response()->json(['ok' => true]);
    }

    private function handleBatchPayment(string $externalReference, ?string $paymentStatus, $paymentId)
    {
        $batchId = str_replace('batch:', '', $externalReference);
        $batch = ReservationBatch::with(['reservations', 'field.venue'])->find($batchId);

        if (!$batch) {
            Log::warning('Batch no encontrado para webhook MP', ['batch_id' => $batchId]);
            return response()->json(['ok' => true]);
        }

        if (!$batch->field || !$batch->field->venue) {
            Log::error('Webhook batch: field o venue eliminado', [
                'batch_id' => $batch->id,
                'field_id' => $batch->field_id ?? null,
            ]);
            return response()->json(['ok' => true]);
        }

        $accessToken = $batch->field->venue->mp_access_token
            ?? config('services.mercadopago.access_token');

        $paymentResponse = Http::withOptions(['verify' => app()->isProduction()])
            ->withToken($accessToken)
            ->get("https://api.mercadopago.com/v1/payments/{$paymentId}");

        if ($paymentResponse->successful()) {
            $paymentStatus = $paymentResponse->json()['status'] ?? $paymentStatus;
        }

        if ($batch->payment_external_id === (string) $paymentId && $batch->status === 'PAID') {
            Log::info('Webhook batch: ya procesado, ignorando', ['payment_id' => $paymentId, 'batch_id' => $batch->id]);
            return response()->json(['ok' => true]);
        }

        $wasPaidBefore = $batch->status === 'PAID';

        \Illuminate\Support\Facades\DB::transaction(function () use ($batch, $paymentId, $paymentStatus) {
            $batch->payment_provider    = 'mercadopago';
            $batch->payment_external_id = (string) $paymentId;
            $batch->payment_status      = $paymentStatus;

            if ($paymentStatus === 'approved') {
                $batch->status     = 'PAID';
                $batch->expires_at = null;

                $batch->reservations()->update([
                    'status'                 => 'PAID',
                    'expires_at'             => null,
                    'payment_provider'       => 'mercadopago',
                    'payment_external_id'    => (string) $paymentId,
                    'payment_status'         => 'approved',
                    'payment_mp_token_owner' => $batch->payment_mp_token_owner,
                ]);
            }

            $batch->save();
        });

        $batch->refresh();

        if ($paymentStatus === 'approved' && !$wasPaidBefore) {
            $batch->loadMissing(['user', 'field.venue.owner']);

            if ($batch->user && $batch->user->email) {
                Mail::to($batch->user->email)
                    ->send(new \App\Mail\BatchReservationPaidMail($batch));
            }

            $venueOwner = $batch->field->venue->owner;
            if ($venueOwner && $venueOwner->email) {
                Mail::to($venueOwner->email)
                    ->send(new \App\Mail\VenueAdminBatchReservationMail($batch));
            }
        }

        return response()->json(['ok' => true]);
    }

    private function handlePlatformPayoutPayment(string $externalReference, ?string $paymentStatus, $paymentId)
    {
        $venueId = str_replace('platform_payout:venue:', '', $externalReference);
        $venue   = Venue::find($venueId);

        if (!$venue) {
            Log::warning('Venue no encontrado para platform_payout webhook', ['venue_id' => $venueId]);
            return response()->json(['ok' => true]);
        }

        if ($venue->mp_access_token) {
            $paymentResponse = Http::withOptions(['verify' => app()->isProduction()])
                ->withToken($venue->mp_access_token)
                ->get("https://api.mercadopago.com/v1/payments/{$paymentId}");

            if ($paymentResponse->successful()) {
                $paymentStatus = $paymentResponse->json()['status'] ?? $paymentStatus;
            }
        }

        if ($paymentStatus === 'approved') {
            $count = PlatformPayout::where('venue_id', $venue->id)
                ->where('status', 'pending')
                ->update([
                    'status'  => 'paid',
                    'paid_at' => now(),
                    'notes'   => "Pagado vía Mercado Pago (payment #{$paymentId})",
                ]);

            Log::info('Platform payouts marcados como pagados vía MP webhook', [
                'venue_id'   => $venueId,
                'count'      => $count,
                'payment_id' => $paymentId,
            ]);
        }

        return response()->json(['ok' => true]);
    }

    private function handleModifyPayment(string $externalReference, ?string $paymentStatus, $paymentId)
    {
        $reservationId = (int) str_replace('modify:', '', $externalReference);
        $reservation   = Reservation::find($reservationId);

        if (!$reservation) {
            Log::warning('handleModifyPayment: reserva no encontrada', ['reservation_id' => $reservationId]);
            return response('OK', 200);
        }

        if ($paymentStatus !== 'approved') {
            return response('OK', 200);
        }

        // Paso 1: verificar idempotencia y leer datos dentro de una transacción corta.
        // applyChange NO va aquí — tiene su propia transacción interna.
        $processData = DB::transaction(function () use ($reservation) {
            $fresh = Reservation::lockForUpdate()->find($reservation->id);

            if (!$fresh || !$fresh->modify_mp_preference_id || $fresh->modification_status === 'COMPLETED') {
                return null;
            }

            $newField = Field::with(['venue', 'price', 'discounts', 'blocks'])->find($fresh->modify_field_id);

            if (!$newField) {
                Log::error('handleModifyPayment: campo pendiente no encontrado', [
                    'reservation_id'  => $fresh->id,
                    'modify_field_id' => $fresh->modify_field_id,
                ]);
                return null;
            }

            $newField->loadMissing('venue');

            if (!$newField->venue) {
                Log::error('handleModifyPayment: venue del campo no encontrado', [
                    'reservation_id' => $fresh->id,
                    'field_id' => $newField->id,
                ]);
                return null;
            }

            $accessToken = $newField->venue->mp_access_token ?? config('services.mercadopago.access_token');
            $newStart    = Carbon::parse($fresh->modify_start_at);
            $newPrice    = (float) $fresh->total_amount + (float) $fresh->modify_diff_amount;

            return [
                'fresh'       => $fresh,
                'newField'    => $newField,
                'accessToken' => $accessToken,
                'newStart'    => $newStart,
                'newPrice'    => $newPrice,
                'diffAmount'  => (float) $fresh->modify_diff_amount,
            ];
        });

        if (!$processData) {
            return response('OK', 200);
        }

        // Paso 2: verificar el estado real del pago con el token correcto (fuera de transacción).
        $paymentResponse = Http::withOptions(['verify' => app()->isProduction()])
            ->withToken($processData['accessToken'])
            ->get("https://api.mercadopago.com/v1/payments/{$paymentId}");

        $verifiedStatus = $paymentResponse->successful()
            ? ($paymentResponse->json()['status'] ?? null)
            : null;

        if ($verifiedStatus !== 'approved') {
            return response('OK', 200);
        }

        // Paso 3: aplicar el cambio. applyChange tiene su propia transacción — no anidar.
        $fresh      = $processData['fresh'];
        $newField   = $processData['newField'];
        $newStart   = $processData['newStart'];
        $newPrice   = $processData['newPrice'];
        $diffAmount = $processData['diffAmount'];

        /** @var ReservationModifyService $modifyService */
        $modifyService = app(ReservationModifyService::class);

        try {
            $modifyService->applyChange($fresh, $newField, $newStart, $newPrice);
        } catch (\Throwable $e) {
            Log::error('handleModifyPayment: slot tomado al aplicar cambio, reembolsando', [
                'reservation_id' => $fresh->id,
                'error'          => $e->getMessage(),
            ]);

            Http::withOptions(['verify' => app()->isProduction()])
                ->withToken($processData['accessToken'])
                ->post("https://api.mercadopago.com/v1/payments/{$paymentId}/refunds", [
                    'amount' => round($diffAmount, 2),
                ]);

            $fresh->update([
                'modify_mp_preference_id' => null,
                'modify_diff_amount'      => null,
                'modify_field_id'         => null,
                'modify_start_at'         => null,
                'modification_status'     => 'REFUNDED',
            ]);

            return response('OK', 200);
        }

        // Paso 4: limpiar datos de modificación pendiente y notificar.
        $fresh->update([
            'modify_mp_preference_id' => null,
            'modify_diff_amount'      => null,
            'modify_field_id'         => null,
            'modify_start_at'         => null,
        ]);

        $freshLoaded = $fresh->fresh()->loadMissing(['user', 'field.venue']);
        Mail::to($freshLoaded->user->email)->queue(new ReservationModifiedMail($freshLoaded, 0, null));

        return response('OK', 200);
    }

    private function fetchAuthorizedPaymentWithVenueToken(string $paymentId): ?array
    {
        // Buscar todas las RecurringSubscription con preapproval_id (tienen venue token)
        $subscriptions = RecurringSubscription::whereNotNull('mp_preapproval_id')
            ->whereIn('status', ['ACTIVE', 'PENDING_PAYMENT'])
            ->with('field.venue')
            ->get();

        foreach ($subscriptions as $sub) {
            $venueToken = $sub->field->venue->mp_access_token ?? null;
            if (!$venueToken) continue;

            $response = Http::withOptions(['verify' => app()->isProduction()])
                ->withToken($venueToken)
                ->get("https://api.mercadopago.com/authorized_payments/{$paymentId}");

            if ($response->successful()) {
                return $response->json();
            }
        }

        return null;
    }

    private function handleRecurringSubscriptionPreapproval(string $preapprovalId, array $preapprovalData)
    {
        $externalReference = $preapprovalData['external_reference'] ?? null;
        $mpStatus          = $preapprovalData['status'] ?? null;

        // Buscar por preapproval_id primero, luego por external_reference
        $subscription = RecurringSubscription::where('mp_preapproval_id', $preapprovalId)->first();

        if (!$subscription && str_starts_with((string) $externalReference, 'recurring_subscription:')) {
            $subscriptionId = (int) explode(':', $externalReference)[1];
            $subscription   = RecurringSubscription::find($subscriptionId);
        }

        if (!$subscription) {
            Log::warning('RecurringSubscription: suscripción no encontrada para preapproval', [
                'preapproval_id'     => $preapprovalId,
                'external_reference' => $externalReference,
            ]);
            return response()->json(['ok' => true]);
        }

        // Guardar preapproval_id si aún no lo tiene
        if (!$subscription->mp_preapproval_id) {
            $subscription->mp_preapproval_id = $preapprovalId;
        }

        $service = app(RecurringReservationSubscriptionService::class);

        try {
            if ($mpStatus === 'authorized') {
                $service->activateSubscription($subscription, $preapprovalData);

                Log::info('RecurringSubscription: suscripción activada via preapproval', [
                    'subscription_id' => $subscription->id,
                    'preapproval_id'  => $preapprovalId,
                ]);
            } elseif ($mpStatus === 'cancelled') {
                $subscription->status                 = 'CANCELLED';
                $subscription->mp_subscription_status = 'cancelled';
                $subscription->cancelled_at           = now();
                $subscription->cancel_reason          = 'mp_cancelled';
                $subscription->save();

                Log::info('RecurringSubscription: suscripción cancelada via preapproval webhook', [
                    'subscription_id' => $subscription->id,
                    'preapproval_id'  => $preapprovalId,
                ]);
            } else {
                $subscription->mp_subscription_status = $mpStatus;
                $subscription->save();
            }
        } catch (\Throwable $e) {
            Log::error('RecurringSubscription: error al procesar preapproval webhook', [
                'subscription_id' => $subscription->id,
                'preapproval_id'  => $preapprovalId,
                'mp_status'       => $mpStatus,
                'error'           => $e->getMessage(),
                'trace'           => $e->getTraceAsString(),
            ]);
        }

        return response()->json(['ok' => true]);
    }

    private function handleRecurringSubscriptionAuthorizedPayment(string $paymentId, array $paymentData)
    {
        $preapprovalId = $paymentData['preapproval_id'] ?? null;
        $status        = $paymentData['status'] ?? null;
        $amount        = (float) ($paymentData['transaction_amount'] ?? 0);

        $subscription = RecurringSubscription::where('mp_preapproval_id', $preapprovalId)->first();

        if (!$subscription) {
            Log::warning('RecurringSubscription: suscripción no encontrada para authorized_payment', [
                'payment_id'     => $paymentId,
                'preapproval_id' => $preapprovalId,
            ]);
            return response()->json(['ok' => true]);
        }

        // Idempotencia: si ya existe un período para este pago, no procesar de nuevo
        $alreadyProcessed = RecurringSubscriptionPeriod::where('mp_authorized_payment_id', $paymentId)->exists();
        if ($alreadyProcessed) {
            return response()->json(['ok' => true]);
        }

        $service = app(RecurringReservationSubscriptionService::class);

        try {
            if ($status === 'processed') {
                $period = $service->generateMonthReservations($subscription, $paymentId, $amount);

                Log::info('RecurringSubscription: mes generado via authorized_payment', [
                    'subscription_id'          => $subscription->id,
                    'period_id'                => $period->id,
                    'mp_authorized_payment_id' => $paymentId,
                    'amount'                   => $amount,
                    'reservations_created'     => $period->reservations_created,
                ]);
            } else {
                $service->handleFailedPayment($subscription, $paymentId, $amount);

                Log::error('RecurringSubscription: pago autorizado con status no procesado', [
                    'subscription_id' => $subscription->id,
                    'payment_id'      => $paymentId,
                    'status'          => $status,
                    'amount'          => $amount,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('RecurringSubscription: error al procesar authorized_payment webhook', [
                'subscription_id' => $subscription->id,
                'payment_id'      => $paymentId,
                'error'           => $e->getMessage(),
                'trace'           => $e->getTraceAsString(),
            ]);
        }

        return response()->json(['ok' => true]);
    }

    private function handleVenueAdminSubscriptionPayment(string $externalReference, ?string $paymentStatus, $paymentId)
    {
        $subscriptionId = str_replace('venue_admin_subscription:', '', $externalReference);

        $subscription = VenueAdminSubscription::with('user')->find($subscriptionId);

        if (!$subscription) {
            Log::warning('Suscripción no encontrada para webhook MP', [
                'subscription_id' => $subscriptionId,
            ]);

            return response()->json(['ok' => true]);
        }

        $subscription->payment_provider    = 'mercadopago';
        $subscription->payment_external_id = (string) $paymentId;
        $subscription->payment_status      = $paymentStatus;

        $wasActiveBefore = $subscription->status === 'ACTIVE';

        if ($paymentStatus === 'approved') {
            $user = $subscription->user;

            $currentActive = $user?->activeVenueAdminSubscription()->first();

            if ($currentActive && $currentActive->id !== $subscription->id && $currentActive->expires_at && $currentActive->expires_at->isFuture()) {
                $startsAt = $currentActive->expires_at->copy();
            } else {
                $startsAt = now();
            }

            $days      = $subscription->billing_cycle === 'annual' ? ($subscription->long_term_months * 30) : 30;
            $expiresAt = $startsAt->copy()->addDays($days);

            $subscription->status     = 'ACTIVE';
            $subscription->starts_at  = $startsAt;
            $subscription->expires_at = $expiresAt;

            if ($user && $user->role === 'user') {
                $user->role = 'venue_admin';
                $user->save();
            }
        } elseif (in_array($paymentStatus, ['rejected', 'cancelled'])) {
            $subscription->status = 'CANCELLED';
        }

        $subscription->save();

        if ($paymentStatus === 'approved' && !$wasActiveBefore) {
            app(\App\Services\ReferralService::class)->processReward($subscription);

            Log::info('Membresía admin activada o renovada', [
                'subscription_id' => $subscription->id,
                'user_id'         => $subscription->user_id,
                'starts_at'       => optional($subscription->starts_at)?->toDateTimeString(),
                'expires_at'      => optional($subscription->expires_at)?->toDateTimeString(),
            ]);
        }

        return response()->json(['ok' => true]);
    }
}
