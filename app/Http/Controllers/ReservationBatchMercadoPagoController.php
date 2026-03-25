<?php

namespace App\Http\Controllers;

use App\Models\ReservationBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReservationBatchMercadoPagoController extends Controller
{
    public function checkout(Request $request, ReservationBatch $batch)
    {
        $user = $request->user();

        if ($batch->user_id !== $user->id && $user->role !== 'super_admin') {
            abort(403, 'No tenés permiso para pagar este lote.');
        }

        if ($batch->status !== 'PENDING_PAYMENT') {
            abort(422, 'Este lote no está pendiente de pago.');
        }

        if ($batch->expires_at && $batch->expires_at->isPast()) {
            abort(422, 'El lote de reservas ya expiró.');
        }

        $batch->loadMissing(['field.venue', 'reservations' => fn($q) => $q->orderBy('start_at')]);
        $venue = $batch->field->venue;

        $accessToken = $venue->mp_access_token ?? config('services.mercadopago.access_token');
        $isMarketplace = !is_null($venue->mp_access_token);

        $baseUrl = rtrim(config('app.url'), '/');

        $items = $batch->reservations->map(function ($r) use ($batch) {
            return [
                'title'       => 'Reserva ' . $r->start_at->locale('es')->isoFormat('ddd D/MM') . ' ' . $r->start_at->format('H:i'),
                'quantity'    => 1,
                'currency_id' => $batch->currency,
                'unit_price'  => (float) $r->total_amount,
            ];
        })->toArray();

        // Add discount as a negative item if applicable
        if ($batch->discount_amount > 0) {
            $items[] = [
                'title'       => "Descuento por reserva recurrente ({$batch->discount_percentage}%)",
                'quantity'    => 1,
                'currency_id' => $batch->currency,
                'unit_price'  => -(float) $batch->discount_amount,
            ];
        }

        $payload = [
            'items'              => $items,
            'external_reference' => 'batch:' . $batch->id,
            'back_urls'          => [
                'success' => $baseUrl . '/batch-success/' . $batch->id,
                'failure' => $baseUrl . '/batch-failure/' . $batch->id,
                'pending' => $baseUrl . '/batch-pending/' . $batch->id,
            ],
            'notification_url' => $baseUrl . '/webhooks/mercadopago',
        ];

        $response = Http::withOptions(['verify' => app()->isProduction()])
            ->withToken($accessToken)
            ->post('https://api.mercadopago.com/checkout/preferences', $payload);

        if (!$response->successful()) {
            Log::error('MercadoPago batch: error al crear preferencia', [
                'batch_id' => $batch->id,
                'status'   => $response->status(),
            ]);
            return back()->with('error', 'No se pudo iniciar el pago. Por favor, intentá de nuevo.');
        }

        $data = $response->json();

        if (!isset($data['init_point'])) {
            Log::error('MercadoPago batch: respuesta sin init_point', [
                'batch_id' => $batch->id,
                'response' => $data,
            ]);
            return back()->with('error', 'Respuesta inesperada de Mercado Pago. Por favor, intentá de nuevo.');
        }

        $batch->update([
            'payment_provider'       => 'mercadopago',
            'mp_preference_id'       => $data['id'] ?? null,
            'payment_mp_token_owner' => $isMarketplace ? 'venue' : 'platform',
        ]);

        return redirect()->away($data['init_point']);
    }
}
