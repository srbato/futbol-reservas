<?php

namespace App\Services;

use App\Models\Reservation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MercadoPagoPartialRefundService
{
    /**
     * Intenta un reembolso parcial por el monto indicado.
     *
     * Retorna:
     *   true  → reembolso procesado correctamente
     *   false → la llamada a MP falló (loguea el error)
     *   null  → no aplica (no era pago de MP o falta payment_external_id)
     */
    public function refundPartial(Reservation $reservation, float $amount): ?bool
    {
        if ($reservation->payment_provider !== 'mercadopago') {
            return null;
        }

        if (!$reservation->payment_external_id) {
            return null;
        }

        if ($amount <= 0) {
            return null;
        }

        $reservation->loadMissing('field.venue');

        if ($reservation->payment_mp_token_owner === 'venue') {
            $accessToken = $reservation->field->venue->mp_access_token;
        } elseif ($reservation->payment_mp_token_owner === 'platform') {
            $accessToken = config('services.mercadopago.access_token');
        } else {
            $accessToken = $reservation->field->venue->mp_access_token
                ?? config('services.mercadopago.access_token');
        }

        $response = Http::withOptions(['verify' => app()->isProduction()])
            ->withToken($accessToken)
            ->withHeaders(['X-Idempotency-Key' => (string) Str::uuid()])
            ->post("https://api.mercadopago.com/v1/payments/{$reservation->payment_external_id}/refunds", [
                'amount' => round($amount, 2),
            ]);

        if ($response->successful()) {
            Log::info('Reembolso parcial MP procesado', [
                'reservation_id' => $reservation->id,
                'payment_id'     => $reservation->payment_external_id,
                'amount'         => $amount,
                'refund'         => $response->json(),
            ]);
            return true;
        }

        Log::error('Error al procesar reembolso parcial MP', [
            'reservation_id' => $reservation->id,
            'payment_id'     => $reservation->payment_external_id,
            'amount'         => $amount,
            'http_status'    => $response->status(),
            'response'       => $response->body(),
        ]);
        return false;
    }
}
