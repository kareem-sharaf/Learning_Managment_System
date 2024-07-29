<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class checkIfStudent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
{
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->role_id == 4) {
            return $next($request);
        } else {
            return response()->json(['error' => 'Unauthorized - Not a student'], 403);
        }
    } else {
        return response()->json(['error' => 'Unauthorized - Not logged in'], 403);
    }
}

}
