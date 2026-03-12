<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MercadoPagoController extends Controller
{
    public function checkout(Request $request, Reservation $reservation)
    {
        $user = $request->user();

        if ($reservation->user_id !== $user->id && $user->role !== 'super_admin') {
            abort(403, 'No tenés permiso para pagar esta reserva.');
        }

        if ($reservation->status !== 'PENDING_PAYMENT') {
            abort(422, 'La reserva no está pendiente de pago.');
        }

        if ($reservation->expires_at && $reservation->expires_at->isPast()) {
            abort(422, 'La reserva ya expiró.');
        }

        $baseUrl = rtrim(config('app.url'), '/');
        $response = Http::withoutVerifying()
            ->withToken(config('services.mercadopago.access_token'))
            ->post('https://api.mercadopago.com/checkout/preferences', [
                'items' => [
                    [
                        'title' => 'Reserva de cancha #' . $reservation->id,
                        'quantity' => 1,
                        'currency_id' => $reservation->currency,
                        'unit_price' => (float) $reservation->total_amount,
                    ]
                ],
                'external_reference' => (string) $reservation->id,
                'back_urls' => [
                    'success' => $baseUrl . '/reservation-success/' . $reservation->id,
                    'failure' => $baseUrl . '/reservation-failure/' . $reservation->id,
                    'pending' => $baseUrl . '/reservation-pending/' . $reservation->id,
                ],
                'notification_url' => $baseUrl . '/webhooks/mercadopago',
            ]);

        if (!$response->successful()) {
            dd($response->status(), $response->json(), $response->body());
        }

        $data = $response->json();

        if (!isset($data['init_point'])) {
            dd('Mercado Pago no devolvió init_point', $data);
        }

        $reservation->update([
            'payment_provider' => 'mercadopago',
            'mp_preference_id' => $data['id'] ?? null,
        ]);

        return redirect()->away($data['init_point']);
    }
}

