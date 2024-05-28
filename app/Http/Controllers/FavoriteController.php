<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Subject;
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
            'category' => 'string|exists:categories,category',
            'teacher' => 'string|exists:users,name',
            'subject' => 'string|exists:subjects,name'
        ]);

        $favoritable_type = '';
        $favoritable_id = '';

        if ($request->has('category')) {
            $category = Category::where('category', $request->category)->first();
            if (!$category) {
                return response()->json(['message' => 'Category not found!'], 404);
            }
            $favoritable_type = 'category';
            $favoritable_id = $category->id;
            $favoritable_name = $category->category;
        } elseif ($request->has('teacher')) {
            $teacher = User::where('name', $request->teacher)->where('role_id', 3)->first();
            if (!$teacher) {
                return response()->json(['message' => 'Teacher not found!'], 404);
            }
            $favoritable_type = 'teacher';
            $favoritable_id = $teacher->id;
            $favoritable_name = $teacher->name;
        } elseif ($request->has('subject')) {
            $subject = Subject::where('name', $request->subject)->first();
            if (!$subject) {
                return response()->json(['message' => 'Subject not found!'], 404);
            }
            $favoritable_type = 'subject';
            $favoritable_id = $subject->id;
            $favoritable_name = $subject->name;
        }

        $favorite = new Favorite([
            'favoritable_id' => $favoritable_id,
            'favoritable_type' => $favoritable_type,
            'favoritable_name' => $favoritable_name
        ]);

        $user_id->favorites()->save($favorite);
        $favorite->users()->attach($user->id);

        return response()->json([
            'message' => 'Favorite stored successfully',
            'favorite' => $favorite
        ], 201);
    }
}
