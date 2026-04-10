<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckOrganizerTier
{
    public function handle(Request $request, Closure $next, string $requiredTier = 'pro'): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $tier = $user->organizerTier();

        if ($requiredTier === 'pro' && $tier !== 'pro') {
            return redirect()->route('organizador.planes')
                ->with('info', 'Esta funcion requiere el plan Pro. Suscribite para desbloquear todas las funcionalidades.');
        }

        return $next($request);
    }
}
