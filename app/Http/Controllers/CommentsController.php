<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\User;
use App\Models\Subject;
use App\Models\Unit;
class CommentsController extends Controller
{
    public function store(Request $request)
{
    $validatedData = $request->validate([
        'content' => 'required|string|max:255',
        'user_id' => 'required|integer|exists:users,id',
        'subject_id' => 'nullable|integer|exists:subjects,id',
        'lesson_id' => 'nullable|integer|exists:lessons,id',

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

    ]);

    $comment->update($validatedData);

    return response()->json($comment);
}

public function destroy(Comment $comment)
{
    $comment->delete();

    return response()->json(null, 204);
}
public function getComments(Request $request)
    {
        $validatedData = $request->validate([
            'lesson_id' => 'required|exists:users,id',
            
        ]);
        $lesson = Lesson::find($validatedData['lesson_id']);
        if (!$lesson) {
            return response()->json(['error' => 'Lesson not found'], 404);
        }

        $comments = $lesson->comments;

        return response()->json($comments);
    }
}
