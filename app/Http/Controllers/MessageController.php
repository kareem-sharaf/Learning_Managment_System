<?php

namespace App\Http\Controllers;

use App\Events\Message;
use App\Models\MessageModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    public function sendmessage(Request $request)
    {
        $sender = Auth::user();
        $sender_role_id= $sender->role_id;

        $validatedData = $request->validate([
            'user_id' => 'required|integer|exists:users,id', // Ensure user_id exists in the users table
            'message' => 'required|string',
        ]);

        $receiver = User::find($validatedData['user_id']);

        // Additional logic to restrict students and teachers to send messages only to admin and manager
        if (($sender_role_id == '4' || $sender_role_id == '3') && ($receiver->role_id == '4' || $receiver->role_id == '3')) {
            return response()->json(['error' => 'not here bro'], 403);
        }


        $message = MessageModel::create([
            'user_id' => $validatedData['user_id'],
            'message' => $validatedData['message'],
        ]);

        event(new Message($message, $request->user_id)); // Assuming your event expects two arguments

        return response()->json([
            'message' => 'Message sent successfully',
            'content' => $message->message,
            'receiver_name' => $receiver->role_id,
            'sender_name' => $sender->role_id,

        ]);
    }

    public function updateMessage(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|integer|exists:message_models,id', // Update to message_models
            'message' => 'required|string',
        ]);

        $message = MessageModel::find($validatedData['id']);
        if (!$message) {
            return response()->json(['message' => 'Message not found'], 404);
        }

        // Ensure only the sender or the receiver can update their own messages
        $user = auth()->user();
        if ($message->user_id != $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message->message = $validatedData['message'];
        $message->save();

        return response()->json([
            'message' => 'Message updated successfully',
            'content' => $message->message,
        ]);
    }

    public function deleteMessage(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|integer|exists:message_models,id', // Update to message_models
        ]);

        $message = MessageModel::find($validatedData['id']);
        if (!$message) {
            return response()->json(['message' => 'Message not found'], 404);
        }

        // Ensure only the sender or an admin/manager can delete the message
        $sender = auth()->user();
        if ($message->user_id != $sender->id && !in_array($sender->role_id, ['1', '2'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message->delete();

        return response()->json(['message' => 'Message deleted successfully']);
    }
}
