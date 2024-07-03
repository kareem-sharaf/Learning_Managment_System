<?php

namespace App\Http\Controllers;

use App\Events\Message;
use App\Models\Message as MessageModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('checkIfAdminOrManager')->only(['sendmessage', 'updateMessage', 'deleteMessage']);
        $this->middleware('checkIfStudentOrTeacher')->only(['sendmessage']);
    }
    
    public function sendmessage(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $sender = auth()->user();
        $receiver = User::find($validatedData['user_id']);

        // Additional logic to restrict student and teacher to send messages only to admin and manager
        if (($sender->role == 'student' || $sender->role == 'teacher') &&
            ($receiver->role != 'admin' && $receiver->role != 'manager')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message = MessageModel::create([
            'sender_id' => $sender->id,
            'receiver_id' => $validatedData['user_id'],
            'message' => $validatedData['message'],
        ]);

        event(new Message($message));

        return response()->json([
            'Message' => 'Message sent successfully',
            'content' => $message->message,
            'user_name' => $receiver->name,
            'sender_name' => $sender->name,
        ]);
    }

    public function updateMessage(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|integer|exists:messages,id',
            'message' => 'required|string',
        ]);
    
        $message = MessageModel::find($validatedData['id']);
        if (!$message) {
            return response()->json(['Message' => 'Message not found'], 404);
        }
    
        // Ensure only the sender or the receiver can update their own messages
        $user = auth()->user();
        if ($message->sender_id != $user->id && $message->receiver_id != $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    
        $message->message = $validatedData['message'];
        $message->save();
    
        return response()->json([
            'Message' => 'Message updated successfully',
            'content' => $message->message,
        ]);
    }
    
    public function deleteMessage(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|integer|exists:messages,id',
        ]);

        $message = MessageModel::find($validatedData['id']);
        if (!$message) {
            return response()->json(['Message' => 'Message not found'], 404);
        }

        // Ensure only the sender or an admin/manager can delete the message
        $sender = auth()->user();
        if ($message->sender_id != $sender->id && $sender->role != 'admin' && $sender->role != 'manager') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message->delete();

        return response()->json(['Message' => 'Message deleted successfully']);
    }
}
