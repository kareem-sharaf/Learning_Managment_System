<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Favorite;
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

    public function store(Request $request)
    {
        $user = Auth::user();
        $user_id = $user->id;

        $request->validate([
            'favoritable_type' => 'required|in:App\Models\Teacher,App\Models\Subject,App\Models\Category',
            'category' => 'string|exists:categories',
            'teacher' => 'string|exists:users',
            'subject' => 'string|exists:subjects'
        ]);

        if ($request->has('category')) {
            $category = Category::where('category', $request->category)
                ->first();
            if (!$category) {
                return response()->json(
                    ['message' => 'Category not found!'],
                    404
                );
            }

            $favoritable_id = $category->id;
        }elseif($request->has('teacher')){
            $teacher = User::where('name', $request->teacher)
            ->first();
            if (!$teacher) {
                return response()->json(
                    ['message' => 'Teacher not found!'],
                    404
                );
            }

        $favoritable_id = $teacher->id;
        }
        $favorite = new Favorite([
            'favoritable_id' => $favoritable_id,
            'favoritable_type' => $request->favoritable_type,
        ]);

        $user_id->favorites()->save($favorite);

        return response()->json([
            'message' => 'Favorite stored successfully',
            'favorite' => $favorite
        ], 201);
    }
}
