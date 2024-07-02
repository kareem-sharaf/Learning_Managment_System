<?php

namespace App\Http\Controllers;

use App\Events\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function sendmessage(Request $request)
{
    $validatedData = $request->validate([
        'user_id' => 'required|exists:users,id',
        'message' => 'required|string',
    ]);

    $user = User::find($validatedData['user_id']);
    $userName = $user->name;

    
    $sender = auth()->user();
    $senderName = $sender->name;

    if (event(new Message($validatedData['user_id'], $validatedData['message'], $senderName))) {
        return response()->json([
            'Message' => 'Message sent successfully',
            'content' => $validatedData['message'],
            'user_name' => $userName,
            'sender_name' => $senderName,
        ]);
    } else {
        return response()->json([
            'Message' => 'Message failed to send',
        ], 500);
    }
}
public function updateMessage(Request $request)
{

    $validatedData = $request->validate([
        'id' => 'required|integer|exists:message,id'
    ]);

    $message = Message::find($validatedData['id']);

    if (!$message) {
        return response()->json([
            'Message' => 'Message not found',
        ], 404);
    }

    $validatedData = $request->validate([
        'message' => 'required|string',
    ]);

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
        'id' => 'required|integer|exists:message,id'
    ]);

    $message = Message::find($validatedData['id']);

    if (!$message) {
        return response()->json([
            'Message' => 'Message not found',
        ], 404);
    }

    $message->delete();

    return response()->json([
        'Message' => 'Message deleted successfully',
    ]);
}
}
