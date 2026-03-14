<?php

namespace App\Services;

use App\Models\Reservation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MercadoPagoRefundService
{
    /**
     * Intenta un reembolso completo para una reserva pagada.
     *
     * Retorna:
     *   true  → reembolso procesado correctamente
     *   false → la llamada a MP falló (loguea el error)
     *   null  → no aplica (no era un pago real de MP)
     */
    public function refund(Reservation $reservation): ?bool
    {
        if ($reservation->status !== 'PAID') {
            return null;
        }

        if ($reservation->payment_provider !== 'mercadopago') {
            return null;
        }

        if (!$reservation->payment_external_id) {
            return null;
        }

        $reservation->loadMissing('field.venue');

        // Usamos el mismo token con el que se cobró originalmente.
        // Si no hay registro (reservas antiguas), caemos al token del venue o plataforma.
        if ($reservation->payment_mp_token_owner === 'venue') {
            $accessToken = $reservation->field->venue->mp_access_token;
        } elseif ($reservation->payment_mp_token_owner === 'platform') {
            $accessToken = config('services.mercadopago.access_token');
        } else {
            $accessToken = $reservation->field->venue->mp_access_token
                ?? config('services.mercadopago.access_token');
        }

        $response = Http::withOptions(['verify' => !app()->isLocal()])
            ->withToken($accessToken)
            ->withHeaders(['X-Idempotency-Key' => (string) Str::uuid()])
            ->post("https://api.mercadopago.com/v1/payments/{$reservation->payment_external_id}/refunds");

        if ($response->successful()) {
            Log::info('Reembolso MP procesado', [
                'reservation_id' => $reservation->id,
                'payment_id'     => $reservation->payment_external_id,
                'refund'         => $response->json(),
            ]);
            return true;
        }

        Log::error('Error al procesar reembolso MP', [
            'reservation_id' => $reservation->id,
            'payment_id'     => $reservation->payment_external_id,
            'http_status'    => $response->status(),
            'response'       => $response->body(),
        ]);
        return false;
    }
}
