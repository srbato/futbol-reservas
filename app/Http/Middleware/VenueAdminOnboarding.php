<?php

namespace App\Http\Middleware;

use App\Models\Venue;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VenueAdminOnboarding
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->needsOnboarding()) {
            return $next($request);
        }

        // No redirigir si ya estamos en rutas de onboarding
        $routeName = $request->route()?->getName();
        if ($routeName && str_starts_with($routeName, 'va.onboarding.')) {
            return $next($request);
        }

        // Calcular el paso actual según el estado real
        $step = $this->calculateCurrentStep($user);

        return redirect()->route('va.onboarding.step', ['step' => $step]);
    }

    private function calculateCurrentStep($user): int
    {
        $venue = Venue::where('owner_user_id', $user->id)->first();

        if (!$venue) {
            return 1;
        }

        $field = $venue->fields()->first();

        if (!$field) {
            return 2;
        }

        $hasSchedules = $field->schedules()->exists();

        if (!$hasSchedules) {
            return 3;
        }

        return 4;
    }
}
