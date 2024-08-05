<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use App\Traits\SendNotificationsService;

class CommentsController extends Controller
{
    use SendNotificationsService;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'content' => 'required|string|max:255',
            'user_id' => 'required|integer|exists:users,id',
            'video_id' => 'required|exists:videos,id',
        ]);

          $user=Auth::user();
          $fcm=$user->fcm;
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $comment = Comment::create($validatedData);
        if($comment){
            $message = [
                'title' => 'Notification Title',
                'body' => 'Notification Body'
            ];
            $this->sendByFcm($fcm,$message);
        }
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

        // Check if the authenticated user matches the user_id of the comment
        if (Auth::id() !== (int)$comment->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validatedData = $request->validate([
            'content' => 'sometimes|required|string|max:255',
            'user_id' => 'sometimes|required|integer|exists:users,id',
            'video_id' => 'required|exists:videos,id',
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

        // Check if the authenticated user matches the user_id of the comment
        if (Auth::id() !== (int)$comment->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
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
