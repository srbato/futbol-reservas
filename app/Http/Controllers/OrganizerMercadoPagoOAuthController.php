<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrganizerMercadoPagoOAuthController extends Controller
{
    public function redirect(Request $request)
    {
        $state = Str::random(40);
        session([
            'mp_oauth_state' => $state,
            'mp_oauth_user_id' => $request->user()->id,
        ]);

        $query = http_build_query([
            'client_id'     => config('services.mercadopago.client_id'),
            'response_type' => 'code',
            'state'         => $state,
            'redirect_uri'  => route('organizador.mp_oauth.callback'),
        ]);

        Log::info('Organizer MP OAuth redirect', ['user_id' => $request->user()->id]);

        return redirect()->away('https://auth.mercadopago.com/authorization?' . $query);
    }

    public function callback(Request $request)
    {
        $state = $request->input('state');
        $code  = $request->input('code');
        $error = $request->input('error');

        if ($error || !$code) {
            session()->forget(['mp_oauth_state', 'mp_oauth_user_id']);
            return redirect()->route('torneos.my')
                ->with('error', 'Conectar Mercado Pago fue cancelado. Por favor, intenta de nuevo.');
        }

        if (!$state || $state !== session('mp_oauth_state')) {
            session()->forget(['mp_oauth_state', 'mp_oauth_user_id']);
            return redirect()->route('torneos.my')
                ->with('error', 'Hubo un error de seguridad. Por favor, intenta de nuevo.');
        }

        $userId = session('mp_oauth_user_id');
        $user = \App\Models\User::find($userId);

        if (!$user || $user->id !== $request->user()->id) {
            abort(403);
        }

        $response = Http::withOptions(['verify' => app()->isProduction()])
            ->post('https://api.mercadopago.com/oauth/token', [
                'client_id'     => config('services.mercadopago.client_id'),
                'client_secret' => config('services.mercadopago.client_secret'),
                'grant_type'    => 'authorization_code',
                'code'          => $code,
                'redirect_uri'  => route('organizador.mp_oauth.callback'),
            ]);

        if (!$response->successful()) {
            Log::error('Organizer MP OAuth token exchange failed', [
                'user_id'  => $userId,
                'response' => $response->body(),
            ]);

            session()->forget(['mp_oauth_state', 'mp_oauth_user_id']);
            return redirect()->route('torneos.my')
                ->with('error', 'No se pudo conectar Mercado Pago. Intenta de nuevo.');
        }

        $data = $response->json();

        $user->mp_access_token  = $data['access_token'];
        $user->mp_refresh_token = $data['refresh_token'] ?? null;
        $user->mp_user_id       = $data['user_id'] ?? null;
        $user->save();

        session()->forget(['mp_oauth_state', 'mp_oauth_user_id']);

        return redirect()->route('torneos.my')
            ->with('success', 'Mercado Pago conectado correctamente.');
    }

    public function disconnect(Request $request)
    {
        $user = $request->user();
        $user->mp_access_token  = null;
        $user->mp_refresh_token = null;
        $user->mp_user_id       = null;
        $user->save();

        return redirect()->route('torneos.my')
            ->with('success', 'Cuenta de Mercado Pago desconectada.');
    }
}
