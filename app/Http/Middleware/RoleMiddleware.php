<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        // Staff de un complejo: acceso permitido sin suscripción propia
        if (in_array('venue_admin', $roles) && $user->isVenueStaff()) {
            return $next($request);
        }

        if (!in_array($user->role, $roles)) {
            abort(403);
        }

        if ($user->role === 'venue_admin' && in_array('venue_admin', $roles)) {
            if (!$user->hasActiveVenueAdminSubscription()) {
                return redirect()
                    ->route('membership.become')
                    ->with('error', 'Tu membresía de socio está vencida o inactiva. Renovala para volver a acceder al panel admin.');
            }
        }

        return $next($request);
    }
}
