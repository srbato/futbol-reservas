<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

        // Cargamos el venue para obtener su token de MP
        $reservation->loadMissing('field.venue.owner');
        $field = $reservation->field;
        $venue = $field?->venue;

        // Revalidar que cancha, venue y owner estén activos
        if (!$field || !$field->is_active) {
            abort(422, 'La cancha ya no está disponible.');
        }
        if (!$venue || !$venue->is_active) {
            abort(422, 'El complejo ya no está disponible.');
        }
        // Owner: si el owner perdió su suscripción venue_admin el complejo ya no debería operar
        $owner = $venue->owner;
        if ($owner && $owner->role !== 'super_admin' && method_exists($owner, 'hasActiveVenueAdminSubscription') && !$owner->hasActiveVenueAdminSubscription()) {
            abort(422, 'Este complejo está temporalmente fuera de servicio.');
        }

        // Si el venue tiene su propio token de MP, lo usamos (marketplace).
        // Si no, usamos el token de TuCancha como fallback.
        $accessToken = $venue->mp_access_token ?? config('services.mercadopago.access_token');
        $isMarketplace = !is_null($venue->mp_access_token);

        $baseUrl = rtrim(config('app.url'), '/');

        $payload = [
            'items' => [
                [
                    'title'       => 'Reserva de cancha #' . $reservation->id,
                    'quantity'    => 1,
                    'currency_id' => $reservation->currency,
                    'unit_price'  => (float) $reservation->total_amount,
                ]
            ],
            'external_reference' => (string) $reservation->id,
            'back_urls' => [
                'success' => $baseUrl . '/reservation-success/' . $reservation->id,
                'failure' => $baseUrl . '/reservation-failure/' . $reservation->id,
                'pending' => $baseUrl . '/reservation-pending/' . $reservation->id,
            ],
            'notification_url' => $baseUrl . '/webhooks/mercadopago',
        ];

        // Sin marketplace por ahora

        try {
            $response = Http::withOptions(['verify' => app()->isProduction()])
                ->withToken($accessToken)
                ->post('https://api.mercadopago.com/checkout/preferences', $payload);
        } catch (\Exception $e) {
            Log::error('MercadoPago: error de conexión al crear preferencia', [
                'reservation_id' => $reservation->id,
                'error'          => $e->getMessage(),
            ]);
            return back()->with('error', 'No se pudo conectar con Mercado Pago. Intentá de nuevo en unos segundos.');
        }

        if (!$response->successful()) {
            Log::error('MercadoPago: error al crear preferencia de pago', [
                'reservation_id' => $reservation->id,
                'status'         => $response->status(),
            ]);
            return back()->with('error', 'No se pudo iniciar el pago. Por favor, intentá de nuevo.');
        }

        $data = $response->json();

        if (!isset($data['init_point'])) {
            Log::error('MercadoPago: respuesta sin init_point', [
                'reservation_id' => $reservation->id,
                'response'       => $data,
            ]);
            return back()->with('error', 'Respuesta inesperada de Mercado Pago. Por favor, intentá de nuevo.');
        }

        $reservation->update([
            'payment_provider'       => 'mercadopago',
            'mp_preference_id'       => $data['id'] ?? null,
            'payment_mp_token_owner' => $isMarketplace ? 'venue' : 'platform',
        ]);

        return redirect()->away($data['init_point']);
    }
}
