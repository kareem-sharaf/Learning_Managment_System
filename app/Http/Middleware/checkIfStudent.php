<?php

namespace App\Http\Middleware;


use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Middleware\checkIfTeacher as Middleware;
use Illuminate\Http\JsonResponse;

class checkIfStudent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (Auth::check() && ($user->role_id == 4)) {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized'], 403);

    }

}
