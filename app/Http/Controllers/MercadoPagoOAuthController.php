<?php

namespace App\Http\Controllers;

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

        $redirectUri = route('va.mp_oauth.callback');

        $query = http_build_query([
            'client_id'     => config('services.mercadopago.client_id'),
            'response_type' => 'code',
            'state'         => $state,
            'redirect_uri'  => $redirectUri,
        ]);

        Log::info('MP OAuth redirect iniciado', ['venue_id' => $venue->id]);

        return redirect()->away('https://auth.mercadopago.com/authorization?' . $query);
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
            return $this->redirectToConnectMp(
                session('mp_oauth_venue_id'),
                'Conectar Mercado Pago fue cancelado. Por favor, intentá de nuevo.'
            );
        }

        // Verificamos el state anti-CSRF
        if (!$state || $state !== session('mp_oauth_state')) {
            return $this->redirectToConnectMp(
                session('mp_oauth_venue_id'),
                'Hubo un error de seguridad en la conexión. Por favor, intentá de nuevo.'
            );
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
        $response = Http::withOptions(['verify' => app()->isProduction()])
            ->post('https://api.mercadopago.com/oauth/token', [
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

            return $this->redirectToConnectMp(
                $venueId,
                'No se pudo conectar la cuenta de Mercado Pago. Por favor, intentá de nuevo.'
            );
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

    private function redirectToConnectMp(?int $venueId, string $error): \Illuminate\Http\RedirectResponse
    {
        session()->forget(['mp_oauth_state', 'mp_oauth_venue_id']);

        $venue = $venueId ? Venue::find($venueId) : null;

        if ($venue) {
            return redirect()->route('va.venues.connect_mp', $venue)->with('error', $error);
        }

        return redirect()->route('va.dashboard')->with('error', $error);
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