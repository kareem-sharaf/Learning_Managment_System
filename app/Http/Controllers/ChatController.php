<?php

namespace App\Http\Controllers;

use App\Events\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function message(Request $request)
    {
        $validatedData = $request->validate([
            'username' => 'required|string|max:255',
            'message' => 'required|string',
        ]);
    
        event(new Message($validatedData['username'], $validatedData['message']));
    
        return response()->json(['message' => 'Message sent successfully']);
    }
}
