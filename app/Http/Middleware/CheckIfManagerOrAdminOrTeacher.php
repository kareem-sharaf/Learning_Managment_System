<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIfManagerOrAdminOrTeacher
{
    public function handle($request, Closure $next)
    {
        if (auth()->check() && (auth()->user()->role_id  == 1|| auth()->user()->role_id == 2|| auth()->user()->role_id == 3)) {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized'], 403);
    }
}
