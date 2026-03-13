<?php

namespace App\Http\Controllers\VenueAdmin;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MercadoPagoOAuthController extends Controller
{
    /**
     * Redirige al venue admin a la pantalla de autorización de Mercado Pago.
     */
    public function redirect(Request $request, Venue $venue)
    {
        if ($venue->owner_user_id !== $request->user()->id && $request->user()->role !== 'super_admin') {
            abort(403);
        }

        // Guardamos state en sesión para verificar al volver (anti-CSRF)
        $state = Str::random(40);
        session([
            'mp_oauth_state' => $state,
            'mp_oauth_venue_id' => $venue->id,
        ]);

        $query = http_build_query([
            'client_id'     => config('services.mercadopago.client_id'),
            'response_type' => 'code',
            'platform_id'   => 'mp',
            'state'         => $state,
            'redirect_uri'  => route('va.mp_oauth.callback'),
        ]);

        return redirect()->away('https://auth.mercadopago.com.ar/authorization?' . $query);
    }

    /**
     * MP redirige aquí con el código. Lo intercambiamos por el access_token del dueño.
     */
    public function callback(Request $request)
    {
        $state   = $request->input('state');
        $code    = $request->input('code');
        $error   = $request->input('error');

        // Si el usuario canceló en MP
        if ($error || !$code) {
            return redirect()->route('va.dashboard')
                ->with('error', 'Conectar Mercado Pago fue cancelado o falló.');
        }

        // Verificamos el state anti-CSRF
        if (!$state || $state !== session('mp_oauth_state')) {
            abort(422, 'Estado inválido en OAuth de Mercado Pago.');
        }

        $venueId = session('mp_oauth_venue_id');
        $venue   = Venue::find($venueId);

        if (!$venue) {
            abort(404, 'Complejo no encontrado.');
        }

        // Verificamos que sigue siendo el dueño
        if ($venue->owner_user_id !== $request->user()->id && $request->user()->role !== 'super_admin') {
            abort(403);
        }

        // Intercambiamos el code por el access_token
        $response = Http::withoutVerifying()->post('https://api.mercadopago.com/oauth/token', [
            'client_id'     => config('services.mercadopago.client_id'),
            'client_secret' => config('services.mercadopago.client_secret'),
            'grant_type'    => 'authorization_code',
            'code'          => $code,
            'redirect_uri'  => route('va.mp_oauth.callback'),
        ]);

        if (!$response->successful()) {
            Log::error('Error al intercambiar código OAuth de MP', [
                'venue_id' => $venueId,
                'response' => $response->body(),
            ]);

            return redirect()->route('va.dashboard')
                ->with('error', 'No se pudo conectar la cuenta de Mercado Pago. Intentá de nuevo.');
        }

        $data = $response->json();

        $venue->mp_access_token  = $data['access_token'];
        $venue->mp_refresh_token = $data['refresh_token'] ?? null;
        $venue->mp_user_id       = $data['user_id'] ?? null;
        $venue->save();

        // Limpiamos la sesión
        session()->forget(['mp_oauth_state', 'mp_oauth_venue_id']);

        return redirect()->route('va.dashboard')
            ->with('success', "Mercado Pago conectado correctamente para {$venue->name}.");
    }

    /**
     * Desconecta la cuenta de MP del venue.
     */
    public function disconnect(Request $request, Venue $venue)
    {
        if ($venue->owner_user_id !== $request->user()->id && $request->user()->role !== 'super_admin') {
            abort(403);
        }

        $venue->mp_access_token  = null;
        $venue->mp_refresh_token = null;
        $venue->mp_user_id       = null;
        $venue->save();

        return redirect()->route('va.dashboard')
            ->with('success', "Cuenta de Mercado Pago desconectada de {$venue->name}.");
    }
}