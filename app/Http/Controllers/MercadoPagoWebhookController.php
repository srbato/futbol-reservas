<?php

namespace App\Http\Controllers;

use App\Mail\ReservationPaidMail;
use App\Models\PlatformPayout;
use App\Models\Reservation;
use App\Models\ReservationBatch;
use App\Models\Venue;
use App\Models\VenueAdminSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MercadoPagoWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('MP webhook recibido', $request->all());

        $type   = $request->input('type');
        $topic  = $request->input('topic');
        $dataId = $request->input('data.id') ?? $request->input('id');

        // Ignoramos merchant_order, MP también manda el evento 'payment' por separado
        if ($topic === 'merchant_order') {
            return response()->json(['ok' => true]);
        }

        if ($type !== 'payment' || !$dataId) {
            return response()->json(['ok' => true]);
        }

        $paymentResponse = Http::withOptions(['verify' => !app()->isLocal()])
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

    private function handleReservationPayment(string $reservationId, ?string $paymentStatus, $paymentId)
    {
        $reservation = Reservation::find($reservationId);

        if (!$reservation) {
            Log::warning('Reserva no encontrada para webhook MP', [
                'reservation_id' => $reservationId,
            ]);
            return response()->json(['ok' => true]);
        }

        // Usamos el token del venue si tiene uno, sino el de TuCancha
        $reservation->loadMissing('field.venue');
        $accessToken = $reservation->field->venue->mp_access_token
            ?? config('services.mercadopago.access_token');

        // Re-consultamos el pago usando el token correcto
        $paymentResponse = Http::withOptions(['verify' => !app()->isLocal()])
            ->withToken($accessToken)
            ->get("https://api.mercadopago.com/v1/payments/{$paymentId}");

        if (!$paymentResponse->successful()) {
            Log::error('No se pudo re-consultar el pago con token del venue', [
                'payment_id' => $paymentId,
                'venue_id'   => $reservation->field->venue->id,
            ]);
            // Continuamos igual con el status que ya teníamos del webhook principal
        }

        // Idempotency: if already processed with this exact payment ID, skip
        if ($reservation->payment_external_id === (string) $paymentId && $reservation->status === 'PAID') {
            Log::info('Webhook reserva: ya procesado, ignorando', ['payment_id' => $paymentId, 'reservation_id' => $reservation->id]);
            return response()->json(['ok' => true]);
        }

        // Amount validation: warn if MP amount doesn't match our expected amount
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

        $reservation->payment_provider    = 'mercadopago';
        $reservation->payment_external_id = (string) $paymentId;
        $reservation->payment_status      = $paymentStatus;

        $wasPaidBefore = $reservation->status === 'PAID';

        if ($paymentStatus === 'approved') {
            $reservation->status    = 'PAID';
            $reservation->expires_at = null;
        }

        $reservation->save();

        if ($paymentStatus === 'approved' && !$wasPaidBefore) {
            $reservation->loadMissing(['user', 'field.venue.owner']);

            // Mail al usuario
            Mail::to($reservation->user->email)
                ->send(new ReservationPaidMail($reservation));

            // Mail al dueño del complejo
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

        $accessToken = $batch->field->venue->mp_access_token
            ?? config('services.mercadopago.access_token');

        $paymentResponse = Http::withOptions(['verify' => !app()->isLocal()])
            ->withToken($accessToken)
            ->get("https://api.mercadopago.com/v1/payments/{$paymentId}");

        if ($paymentResponse->successful()) {
            $paymentStatus = $paymentResponse->json()['status'] ?? $paymentStatus;
        }

        // Idempotency: if already processed with this exact payment ID, skip
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

            Mail::to($batch->user->email)
                ->send(new \App\Mail\BatchReservationPaidMail($batch));

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

        // Re-consultamos el pago con el token del venue para confirmar
        if ($venue->mp_access_token) {
            $paymentResponse = Http::withOptions(['verify' => !app()->isLocal()])
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

        $subscription->payment_provider = 'mercadopago';
        $subscription->payment_external_id = (string) $paymentId;
        $subscription->payment_status = $paymentStatus;

        $wasActiveBefore = $subscription->status === 'ACTIVE';

        if ($paymentStatus === 'approved') {
            $user = $subscription->user;

            $currentActive = $user?->activeVenueAdminSubscription()->first();

            if ($currentActive && $currentActive->id !== $subscription->id && $currentActive->expires_at && $currentActive->expires_at->isFuture()) {
                $startsAt = $currentActive->expires_at->copy();
            } else {
                $startsAt = now();
            }

            $days = $subscription->billing_cycle === 'annual' ? 365 : 30;
            $expiresAt = $startsAt->copy()->addDays($days);

            $subscription->status = 'ACTIVE';
            $subscription->starts_at = $startsAt;
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
                'user_id' => $subscription->user_id,
                'starts_at' => optional($subscription->starts_at)?->toDateTimeString(),
                'expires_at' => optional($subscription->expires_at)?->toDateTimeString(),
            ]);
        }

        return response()->json(['ok' => true]);
    }
}