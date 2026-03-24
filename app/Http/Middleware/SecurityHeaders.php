<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // HSTS: solo en producción para no romper localhost con HTTP
        if (!app()->isLocal()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains'
            );
        }

        // Content-Security-Policy
        // Dominios externos usados por la app:
        //   - js.pusher.com / cdn.jsdelivr.net / unpkg.com  → scripts WebSocket y librerías
        //   - fonts.googleapis.com / fonts.gstatic.com      → Google Fonts
        //   - maps.googleapis.com / maps.gstatic.com        → Google Maps
        //   - *.mercadopago.com / *.mercadolibre.com        → Mercado Pago Checkout Pro
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' https://js.pusher.com https://cdn.jsdelivr.net https://unpkg.com https://maps.googleapis.com https://sdk.mercadopago.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://unpkg.com https://cdn.jsdelivr.net",
            "font-src 'self' https://fonts.gstatic.com data:",
            "img-src 'self' data: blob: https://maps.googleapis.com https://maps.gstatic.com https://lh3.googleusercontent.com",
            "connect-src 'self' wss: https://maps.googleapis.com https://api.mercadopago.com",
            "frame-src https://*.mercadopago.com https://*.mercadolibre.com",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ]);

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
