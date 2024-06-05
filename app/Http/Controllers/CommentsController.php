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
        'subject_id' => 'nullable|integer|exists:subjects,id',
        'lesson_id' => 'nullable|integer|exists:lessons,id',
            'video_id' => 'required|exists:videos.id',
            'unit_id' => 'nullable|integer|exists:units,id',
    ]);

    $comment = Comment::create($validatedData);

    return response()->json($comment, 201);
}

public function update(Request $request, Comment $comment)
{
    $validatedData = $request->validate([
        'content' => 'sometimes|required|string|max:255',
        'user_id' => 'sometimes|required|integer|exists:users,id',
        'subject_id' => 'sometimes|nullable|integer|exists:subjects,id',
        'unit_id' => 'sometimes|nullable|integer|exists:units,id',
        'lesson_id' => 'nullable|integer|exists:lessons,id',
        'video_id' => 'required|exists:videos.id',

    ]);

    $comment->update($validatedData);

    return response()->json($comment);
}

public function getComments(Request $request)
{
    $validatedData = $request->validate([
        'video_id' => 'required|exists:videos,id'
    ]);

    $video = Video::find($validatedData['video_id']);

    if (!$video) {
        return response()->json(['error' => 'Video not found'], 404);
    }

    $comments = Comment::where('video_id', $validatedData['video_id'])->get();

    return response()->json($comments);
}
}
