<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\User;
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

        $user = Auth::user();

        $fcm=$user->fcm;

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $comment = Comment::create([
            'content' => $validatedData['content'],
            'video_id' => $validatedData['video_id'],
            'user_id' => $user->id,
        ]);



        return response()->json([
            'Text' => $comment->content,
            'Id' => $comment->id,
            'Student name' => $user->name,
            'Replies' => []
        ], 201);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|integer|exists:comments,id',
        ]);

        $comment = Comment::find($validatedData['id']);

        if (!$comment) {
            return response()->json(['error' => 'Comment not found'], 404);
        }
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validatedData = $request->validate([
            'content' => 'sometimes|required|string|max:255',
            'user_id' => 'sometimes|required|integer|exists:users,id',
            'video_id' => 'required|exists:videos,id',
        ]);

        if($user->role_id=3){
            $validatedData = $request->validate([
                'content' => 'sometimes|required|string|max:255',
                'video_id' => 'sometimes|required|exists:videos,id',
            ]);
        }
        $comment->update($validatedData);
        return response()->json($comment);
    }

    public function destroy(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|integer|exists:comments,id',
        ]);

        $comment = Comment::find($validatedData['id']);

        if (!$comment) {
            return response()->json(['error' => 'Comment not found'], 404);
        }
        $user = Auth::user();

        if (!$user) {
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
            'video_id' => 'required',
        ]);


        $comments = Comment::where('video_id', $validatedData['video_id'])
            ->whereNull('reply_to')
            ->with('replies.user')
            ->get();

        if ($comments->isEmpty()) {
            return response()->json(['error' => 'No comments found'], 404);
        }

        $formattedComments = $comments->map(function ($comment) {
            return [
                'Text' => $comment->content,
                'Id' => $comment->id,
                'Student name' => $comment->user->name,
                'Replies' => $comment->replies->map(function ($reply) {
                    return [
                        'Text' => $reply->content,
                        'Id' => $reply->id,
                        'Teacher name' => $reply->user->name,
                    ];
                })->toArray(),
            ];
        });

        return response()->json($formattedComments);
    }



    public function teacherReply(Request $request)
    {
        $validatedData = $request->validate([
            'content' => 'required|string|max:255',
            'r'=>'required'
        ]);
        $commentId=$request->r;
        $comment = Comment::find($commentId);

        if (!$comment) {
            return response()->json(['error' => 'Comment not found'], 404);
        }

        $user = Auth::user();
        if ($user->role_id!=3) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $reply = new Comment;
        $reply->content = $validatedData['content'];
        $reply->user_id = $user->id;
        $reply->video_id = $comment->video_id;
        $reply->reply_to = $commentId;
        $reply->save();


        return response()->json([
            'Text' => $reply->content,
            'Id' => $reply->id,
            'Teacher name' => $user->name,

        ], 201);
    }
}
