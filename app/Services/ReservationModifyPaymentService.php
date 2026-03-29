<?php

namespace App\Services;

use App\Models\Field;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReservationModifyPaymentService
{
    /**
     * Crea una preferencia de MercadoPago para cobrar la diferencia de precio
     * cuando el nuevo horario es más caro que el original.
     *
     * Persiste los datos del cambio pendiente en la reserva y retorna el init_point.
     *
     * @throws \RuntimeException si la API de MP no responde correctamente.
     */
    public function createPreference(
        Reservation $reservation,
        Field $newField,
        Carbon $newStart,
        float $diff
    ): string {
        $reservation->loadMissing('field.venue');
        $newField->loadMissing('venue');

        // Resolver el access_token correcto (misma lógica que el resto del sistema)
        $accessToken = $newField->venue->mp_access_token
            ?? config('services.mercadopago.access_token');

        $payload = [
            'items' => [
                [
                    'title'       => "Diferencia de modificación — Reserva #{$reservation->id}",
                    'quantity'    => 1,
                    'unit_price'  => round((float) $diff, 2),
                    'currency_id' => $reservation->currency ?? 'ARS',
                ],
            ],
            'external_reference' => "modify:{$reservation->id}",
            'back_urls'          => [
                'success' => route('reservations.modify.payment.success', $reservation),
                'failure' => route('reservations.modify.payment.failure', $reservation),
                'pending' => route('reservations.modify.payment.pending', $reservation),
            ],
            'auto_return'        => 'approved',
            'notification_url'   => route('webhooks.mercadopago'),
        ];

        $response = Http::withOptions(['verify' => app()->isProduction()])
            ->withToken($accessToken)
            ->post('https://api.mercadopago.com/checkout/preferences', $payload);

        if (!$response->successful()) {
            Log::error('ReservationModifyPaymentService: error al crear preferencia MP', [
                'reservation_id' => $reservation->id,
                'http_status'    => $response->status(),
                'response'       => $response->body(),
            ]);
            throw new \RuntimeException('No se pudo crear la preferencia de pago en MercadoPago.');
        }

        $data       = $response->json();
        $initPoint  = $data['init_point'] ?? null;
        $preferenceId = $data['id'] ?? null;

        if (!$initPoint) {
            throw new \RuntimeException('MercadoPago no retornó un init_point válido.');
        }

        // Persistir datos del cambio pendiente en la reserva
        $reservation->update([
            'modify_mp_preference_id' => $preferenceId,
            'modify_diff_amount'      => round((float) $diff, 2),
            'modify_field_id'         => $newField->id,
            'modify_start_at'         => $newStart->toDateTimeString(),
        ]);

        Log::info('ReservationModifyPaymentService: preferencia creada', [
            'reservation_id' => $reservation->id,
            'preference_id'  => $preferenceId,
            'diff'           => $diff,
        ]);

        return $initPoint;
    }
}
