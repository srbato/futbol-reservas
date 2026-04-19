<?php

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Primero: redirigir www.* → apex con 301 (antes que cualquier otra cosa)
        $middleware->prepend(\App\Http\Middleware\RedirectWwwToApex::class);
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        $middleware->alias([
            'active.user'        => \App\Http\Middleware\EnsureUserIsActive::class,
            'role'               => \App\Http\Middleware\RoleMiddleware::class,
            'venue.mp'           => \App\Http\Middleware\EnsureVenueMpConnected::class,
            'venue.onboarding'   => \App\Http\Middleware\VenueAdminOnboarding::class,
            'organizer.tier'     => \App\Http\Middleware\CheckOrganizerTier::class,
            'torneos.soon'       => \App\Http\Middleware\TournamentsComingSoon::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'webhooks/mercadopago',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->report(function (HttpException $e) {
            if ($e->getStatusCode() === 403) {
                Log::warning('Acceso denegado (403)', [
                    'url'     => request()->fullUrl(),
                    'method'  => request()->method(),
                    'user_id' => auth()->id() ?? 'guest',
                    'ip'      => request()->ip(),
                ]);
            }
        })->stop();
    })->create();