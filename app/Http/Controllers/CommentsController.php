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
            'lesson_id' => 'required|exists:lessons,id',
        ]);

        $user = Auth::user();

        if (!$user) {
            return response()->json(['data' => ['error' => 'Unauthorized']], 403);
        }

        $comment = Comment::create([
            'content' => $validatedData['content'],
            'lesson_id' => $validatedData['lesson_id'],
            'user_id' => $user->id,
        ]);

        return response()->json([
            'data' => [
                'Text' => $comment->content,
                'Id' => $comment->id,
                'name' => $user->name,
                'Replies' => []
            ]
        ], 201);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|integer|exists:comments,id',
        ]);

        $comment = Comment::find($validatedData['id']);

        if (!$comment) {
            return response()->json(['data' => ['error' => 'Comment not found']], 404);
        }

        $user = Auth::user();

        if (!$user) {
            return response()->json(['data' => ['error' => 'Unauthorized']], 403);
        }

        if ($user->role_id == 3) {
            $request->validate([
                'content' => 'sometimes|required|string|max:255',
                'lesson_id' => 'sometimes|required|exists:lessons,id',
            ]);
        } else {
            $request->validate([
                'content' => 'required|string|max:255',
                'lesson_id' => 'required|exists:lessons,id',
            ]);
        }

        $comment->update($request->all());

        return response()->json(['data' => $comment]);
    }

    public function destroy(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|integer|exists:comments,id',
        ]);

        $comment = Comment::find($validatedData['id']);

        if (!$comment) {
            return response()->json(['data' => ['error' => 'Comment not found']], 404);
        }

        $user = Auth::user();

        if (!$user || $comment->user_id !== $user->id) {
            return response()->json(['data' => ['error' => 'Unauthorized']], 403);
        }

        if ($comment->delete()) {
            return response()->json(['data' => ['message' => 'Comment deleted successfully']], 200);
        } else {
            return response()->json(['data' => ['message' => 'Comment not deleted']], 400);
        }
    }

    public function getComments(Request $request)
    {
        $validatedData = $request->validate([
            'lesson_id' => 'required|integer|exists:lessons,id',
        ]);

        $comments = Comment::where('lesson_id', $validatedData['lesson_id'])
            ->whereNull('reply_to')
            ->with(['user', 'replies.user'])
            ->get();

        if ($comments->isEmpty()) {
            return response()->json(['data' => []], 200);
        }

        $formattedComments = $comments->map(function ($comment) {
            return [
                'Text' => $comment->content,
                'Id' => $comment->id,
                'name' => $comment->user->name,
                'Replies' => $comment->replies->map(function ($reply) {
                    return [
                        'Text' => $reply->content,
                        'Id' => $reply->id,
                        'name' => $reply->user->name,
                    ];
                })->toArray(),
            ];
        });

        return response()->json(['data' => $formattedComments], 200);
    }

    public function teacherReply(Request $request)
    {
        $validatedData = $request->validate([
            'content' => 'required|string|max:255',
            'r' => 'required|integer|exists:comments,id',
        ]);

        $commentId = $validatedData['r'];
        $comment = Comment::find($commentId);

        if (!$comment) {
            return response()->json(['data' => ['error' => 'Comment not found']], 404);
        }

        $user = Auth::user();

        if ($user->role_id != 3) {
            return response()->json(['data' => ['error' => 'Unauthorized']], 403);
        }

        $reply = new Comment();
        $reply->content = $validatedData['content'];
        $reply->user_id = $user->id;
        $reply->lesson_id = $comment->lesson_id;
        $reply->reply_to = $commentId;
        $reply->save();

        return response()->json([
            'data' => [
                'Text' => $reply->content,
                'Id' => $reply->id,
                'name' => $user->name,
            ]
        ], 201);
    }
}
