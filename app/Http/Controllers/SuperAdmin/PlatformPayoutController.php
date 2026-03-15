<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\PlatformPayout;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PlatformPayoutController extends Controller
{
    public function index()
    {
        // Totales pendientes por venue
        $pendingByVenue = PlatformPayout::with(['venue', 'reservation.field', 'referralReward.referred'])
            ->where('status', 'pending')
            ->latest()
            ->get()
            ->groupBy('venue_id');

        $paidRecent = PlatformPayout::with(['venue', 'reservation.field'])
            ->where('status', 'paid')
            ->latest('paid_at')
            ->limit(30)
            ->get();

        $totalPending = PlatformPayout::where('status', 'pending')->sum('amount');
        $totalPaid    = PlatformPayout::where('status', 'paid')->sum('amount');

        return view('sa.payouts.index', compact(
            'pendingByVenue',
            'paidRecent',
            'totalPending',
            'totalPaid',
        ));
    }

    public function markPaid(Request $request, PlatformPayout $payout)
    {
        if ($payout->status === 'paid') {
            return back()->with('error', 'Este pago ya fue marcado como pagado.');
        }

        $data = $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $payout->update([
            'status'  => 'paid',
            'paid_at' => now(),
            'notes'   => $data['notes'] ?? null,
        ]);

        return back()->with('success', 'Pago marcado como realizado.');
    }

    public function markVenuePaid(Request $request, Venue $venue)
    {
        $data = $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $count = PlatformPayout::where('venue_id', $venue->id)
            ->where('status', 'pending')
            ->update([
                'status'  => 'paid',
                'paid_at' => now(),
                'notes'   => $data['notes'] ?? null,
            ]);

        if ($count === 0) {
            return back()->with('error', 'No había pagos pendientes para este venue.');
        }

        return back()->with('success', "Se marcaron {$count} pagos como realizados para {$venue->name}.");
    }

    public function payViaMP(Venue $venue)
    {
        $pendingPayouts = PlatformPayout::where('venue_id', $venue->id)
            ->where('status', 'pending')
            ->get();

        if ($pendingPayouts->isEmpty()) {
            return back()->with('error', 'No hay pagos pendientes para este venue.');
        }

        if (!$venue->mp_access_token) {
            return back()->with('error', 'El venue no tiene Mercado Pago conectado. No se puede pagar automáticamente.');
        }

        $total    = $pendingPayouts->sum('amount');
        $currency = $pendingPayouts->first()->currency;
        $baseUrl  = rtrim(config('app.url'), '/');

        $payload = [
            'items' => [
                [
                    'title'       => "Pago plataforma → {$venue->name} ({$pendingPayouts->count()} reservas gratis)",
                    'quantity'    => 1,
                    'currency_id' => $currency,
                    'unit_price'  => (float) $total,
                ],
            ],
            'external_reference' => "platform_payout:venue:{$venue->id}",
            'back_urls' => [
                'success' => $baseUrl . '/sa/payouts/venue/' . $venue->id . '/success',
                'failure' => $baseUrl . '/sa/payouts/venue/' . $venue->id . '/failure',
                'pending' => $baseUrl . '/sa/payouts/venue/' . $venue->id . '/pending',
            ],
            'notification_url' => $baseUrl . '/webhooks/mercadopago',
        ];

        $response = Http::withOptions(['verify' => !app()->isLocal()])
            ->withToken($venue->mp_access_token)
            ->post('https://api.mercadopago.com/checkout/preferences', $payload);

        if (!$response->successful()) {
            Log::error('Error al crear preferencia MP para pago a venue', [
                'venue_id' => $venue->id,
                'response' => $response->body(),
            ]);
            return back()->with('error', 'No se pudo iniciar el pago con Mercado Pago. Intentá de nuevo.');
        }

        $data = $response->json();

        if (!isset($data['init_point'])) {
            return back()->with('error', 'Respuesta inesperada de Mercado Pago.');
        }

        return redirect()->away($data['init_point']);
    }
}
