<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        $user_id = Auth::id();

        $request->validate([
            'sentiment' => 'required|numeric|between:1.0,5.0',
            'reason' => 'required|string',
            'type' => 'required|boolean', // 0 for web, 1 for mobile
        ]);

        $feedback = Feedback::create([
            'sentiment' => $request->sentiment,
            'text' => $request->text,
            'type' => $request->type,
            'user_id' => $user_id
        ]);

        if ($feedback) {
            return response()->json([
                'message' => 'Feedback submitted successfully',
                'data' => $feedback,
            ], 200);
        } else {
            return response()->json([
                'messsage' => 'Trouble submitting feedback'
            ], 404);
        }
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        if (!in_array($request->type, [0, 1])) {
            return response()->json(['error' => 'Invalid type'], 400);
        }

        $feedbacks = Feedback::where('type', $request->type)->get();

        if ($feedbacks) {
            return response()->json([
                'message' => 'Feedback retrieved successfully',
                'data' => $feedbacks,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Could\'nt retrieve feedbacks'
            ], 404);
        }
    }

    public function show(Request $request)
    {
        $user = Auth::user();

        $feedback = Feedback::findOrFail($request->id);

        return response()->json([
            'message' => 'Feedback retrieved successfully',
            'data' => $feedback
        ], 200);
    }

    public function destroy(Request $request)
    {
        $user_id = Auth::id();

        $feedback = Feedback::findOrFail($request->id)->where('user_id', $user_id)->first();

        if ($feedback->delete()) {
            return response()->json([
                'message' => 'Feedback deleted successfully'
            ], 400);
        } else {
            return response()->json([
                'message' => 'Failed to delete feedback',
            ], 400);
        }
    }
}
