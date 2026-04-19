<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Redirect www.host → host (apex) with a 301.
 * Lightweight; no exit() — plays nicely with queues, tests, artisan.
 */
class RedirectWwwToApex
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = (string) $request->getHost();
        if (str_starts_with($host, 'www.')) {
            $apex = substr($host, 4);
            $scheme = $request->getScheme() ?: 'https';
            $uri = $request->getRequestUri();
            return redirect($scheme . '://' . $apex . $uri, 301);
        }

        return $next($request);
    }
}
