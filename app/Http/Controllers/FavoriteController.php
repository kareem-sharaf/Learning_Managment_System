<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(
                ['error' => 'User not found'],
                404
            );
        }
        $favorites = $user->favorites;
        if ($favorites->isNotEmpty()) {
            return response()->json(
                ['message' => $favorites],
                200
            );
        } else {
            return response()->json(
                ['error' => 'You do not have favorites']
            );
        }
    }
}
