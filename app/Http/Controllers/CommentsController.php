<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\User;
use App\Models\Subject;

use App\Models\Video;
class CommentsController extends Controller
{
    public function store(Request $request)
{
    $validatedData = $request->validate([
        'content' => 'required|string|max:255',
        'user_id' => 'required|integer|exists:users,id',
       
            'video_id' => 'required|',
    ]);

    $comment = Comment::create($validatedData);

    return response()->json($comment, 201);
}

public function update(Request $request)
{
    $validatedData = $request->validate([
        'id' => 'required|integer|exists:comments,id'
    ]);

    $comment = Comment::find($validatedData['id']);

    if (!$comment) {
        return response()->json(['error' => 'Comment not found'], 404);
    }

    $validatedData = $request->validate([
        'content' => 'sometimes|required|string|max:255',
        'user_id' => 'sometimes|required|integer|exists:users,id',
        
        'video_id' => 'required|',

    ]);

    $comment->update($validatedData);

    return response()->json($comment);
}

public function destroy(Request $request)
{
    $validatedData = $request->validate([
        'id' => 'required|integer|exists:comments,id'
    ]);

    $comment = Comment::find($validatedData['id']);

    if (!$comment) {
        return response()->json(['error' => 'Comment not found'], 404);
    }

    if ($comment->delete()) {
        return response()->json(['message' => 'Comment deleted successfully'], 200);
    } else {
        return response()->json(['message' => 'Comment not deleted'], 400);
    }
}

public function getComments(Request $request)
{
    $validatedData = $request->validate([
        'video_id' => 'required|exists:videos,id'
    ]);

    $comments = Comment::whereHas('video', function ($query) use ($validatedData) {
        $query->where('id', $validatedData['video_id']);
    })->get();

    if ($comments->isEmpty()) {
        return response()->json(['error' => 'Video not found or no comments found'], 404);
    }

    return response()->json($comments);
}
}
