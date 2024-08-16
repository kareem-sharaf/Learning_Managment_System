<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Favorite;
use App\Models\User;
use App\Models\Subject;
use App\Models\Category;

class FavoriteController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(
                ['message' => 'User not found'],
                404
            );
        }


        if ($user->device_id == null) {
            return response()->json(['message' => 'the device_id is null'], 403);
        }
        $favorites = $user->favorites;
        if ($favorites->isNotEmpty()) {
            return response()->json(
                ['message' => $favorites],
                200
            );
        } else {
            return response()->json(
                ['message' => 'You do not have favorites']
            );
        }
    }

    public function toggle(Request $request)
    {
        $user = Auth::user();


        if ($user->device_id == null) {
            return response()->json(['message' => 'the device_id is null'], 403);
        }

        $request->validate([
            'category' => 'string|exists:categories,category|nullable',
            'teacher' => 'string|exists:users,name|nullable',
            'subject' => 'string|exists:subjects,name|nullable'
        ]);

        $favoritable = null;

        if ($request->has('category')) {
            $favoritable = Category::where('category', $request->category)->first();
            $favoritable_name = $favoritable->category;
        } elseif ($request->has('teacher')) {
            $favoritable = User::where('name', $request->teacher)->where('role_id', 3)->first();
            $favoritable_name = $favoritable->name;
        } elseif ($request->has('subject')) {
            $favoritable = Subject::where('name', $request->subject)->first();
            $favoritable_name = $favoritable->name;
        }

        if (!$favoritable) {
            return response()->json(['message' => 'Item not found!'], 404);
        }

        $existingFavorite = Favorite::where('user_id', $user->id)
            ->where('favoritable_id', $favoritable->id)
            ->where('favoritable_type', get_class($favoritable))
            ->first();

        if ($existingFavorite) {
            $existingFavorite->delete();
            return response()->json(['message' => 'Successfully deleted'], 200);
        }

        $favorite = new Favorite([
            'user_id' => $user->id,
            'favoritable_id' => $favoritable->id,
            'favoritable_type' => get_class($favoritable),
            'favoritable_name' => $favoritable_name,
        ]);

        $favorite->save();

        return response()->json([
            'message' => 'Favorite stored successfully',
            'favorite' => $favorite
        ], 201);
    }
}
