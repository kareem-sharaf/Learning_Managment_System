<?php

namespace App\Http\Controllers;

use App\Events\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function message(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);
    


        
    $user = User::find($validatedData['user_id']); // assuming you have a User model
    $userName = $user->name; // assuming the user model has a `name` attribute

if(
    event(new Message($validatedData['user_id'], $validatedData['message']))

){
    return response()->json([
        'Message' => 'Message sent successfully',
        'content' => $validatedData['message'],
        'user_name' => $userName,
    ]);}else{

        return response()->json([
            'Message' => 'Message failed to send',
        ], 500);
    }
}
}
