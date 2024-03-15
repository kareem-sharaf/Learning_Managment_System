<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    //     Create users
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'father_name' =>'required|string',
            'phone_number' => 'required|unique:users|numeric|starts with:09|min_digits:10|max_digits:10',
            'password' => 'required|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|min:8',
            'password_confirmation' => 'required|same:password',
            'email' => 'email|unique:users',
            'address' => 'required|string',
            'birth_date' => 'required|date',
            'device_id' => 'required|string'
        ]);

        $user = new User([
            'name' => $request->name,
            'father_name' => $request->father_name,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'email' => $request->email,
            'address' => $request->address,
            'birth_date' => $request->birth_date,
            'device_id' => $request->device_id
        ]);

        if ($user->save()) {
            $token = $user->createToken('Personal Access Token')->plainTextToken;
            Auth::login($user, $remember = true);
            return response()->json(
                ['message' => 'successfully created user!', 'accessToken' => $token],
                201
            );
        } else {
            return response()->json(
                ['error' => 'provide proper details'],
                422
            );
        }
    }

    //  login user
    public function login(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|numeric',
            'password' => 'required',
            'device_id' => 'required|string'
        ]);

        $credentials = $request->only('phone_number', 'password');

        if (Auth::attempt($credentials)) {
            $user = User::where('phone_number', $request->phone_number)->first();

            // Validate device ID
            if ($user->device_id !== $request->device_id) {
                Auth::logout();
                return response()->json(['error' => 'Unauthorized device.'], 401);
            }

            // Create and return access token
            $token = $user->createToken('Personal Access Token')->plainTextToken;
            return response()->json(['accessToken' => $token], 200);
        } else {
            return response()->json(['error' => 'Invalid credentials.'], 401);
        }
    }


    //     logout
    public function logout()
    {
        $user =Auth::user();
        $user->token = null;
        $user->device_id = null;

        return response()->json(
            ['message' => 'Successfully logged out']
        );
    }
}
