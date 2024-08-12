<?php

namespace App\Http\Middleware;

use Closure;

class CheckIfManagerOrAdmin
{
    public function handle($request, Closure $next)
    {
        if (auth()->check() && (auth()->user()->role_id  == 1|| auth()->user()->role_id == 2)) {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized'], 403);
    }
}

