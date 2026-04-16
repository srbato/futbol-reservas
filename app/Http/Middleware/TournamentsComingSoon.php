<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TournamentsComingSoon
{
    public function handle(Request $request, Closure $next)
    {
        return response()->view('torneos.coming-soon', [], 200);
    }
}
