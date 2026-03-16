<?php

namespace App\Http\Middleware;

use App\Models\Venue;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureVenueMpConnected
{
    // Rutas que no requieren MP conectado
    private const EXEMPT = [
        'va.venues.create',
        'va.venues.store',
        'va.venues.connect_mp',
        'va.mp_oauth.redirect',
        'va.mp_oauth.callback',
        'va.mp_oauth.disconnect',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Solo aplica a venue_admin (super_admin y staff están exentos)
        if (!$user || $user->role !== 'venue_admin' || $user->isVenueStaff()) {
            return $next($request);
        }

        // Rutas exentas
        if (in_array($request->route()?->getName(), self::EXEMPT)) {
            return $next($request);
        }

        $venue = Venue::where('owner_user_id', $user->id)->first();

        // Sin venue todavía: no hay nada que forzar
        if (!$venue) {
            return $next($request);
        }

        // Tiene venue pero sin MP conectado
        if (!$venue->mp_access_token) {
            return redirect()->route('va.venues.connect_mp', $venue)
                ->with('error', 'Necesitás conectar tu cuenta de Mercado Pago para continuar.');
        }

        return $next($request);
    }
}
