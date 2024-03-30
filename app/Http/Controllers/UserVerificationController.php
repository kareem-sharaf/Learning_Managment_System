<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\UserVerification;
use App\Mail\EmailVerification;
use App\Models\User;


class UserVerificationController extends Controller
{

    //  create a user (web) and generate a random code for verfication
    public function createUserWeb(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'email' => 'required|email|unique:users',
            'role_id' => 'required|numeric'
        ]);

        if ($user->role_id == 1 || ($user->role_id == 2 && $request->role_id == 2)) {
            $length = 7;
            $characters = '00112233445566778899abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $verificationCode = substr(str_shuffle($characters), 0, $length);

            $expiryDate = now()->addHours(24);

            $user = new UserVerification([
                'email' => $request->email,
                'role_id' => $request->role_id,
                'verificationCode' => $verificationCode,
                'expiry_date' => $expiryDate
            ]);

            if ($user->save()) {

                Mail::to($user->email)->send(new EmailVerification($verificationCode));

                return response()->json(
                    [
                        'success' => 'successfully created user!',
                        'status' => 'waiting for verification'
                    ],
                    200
                );
            }
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    //  create users (mobile) and generate a random code for verfication
    public function createUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users'
        ]);

        $length = 7;
        $characters = '00112233445566778899abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $verificationCode = substr(str_shuffle($characters), 0, $length);

        $expiryDate = now()->addHours(24);

        $user = new UserVerification([
            'email' => $request->email,
            'role_id' => 4,
            'verificationCode' => $verificationCode,
            'expiry_date' => $expiryDate
        ]);

        if ($user->save()) {

            Mail::to($user->email)->send(new EmailVerification($verificationCode));

            return response()->json(
                [
                    'success' => 'successfully created user!',
                    'status' => 'waiting for verification'
                ],
                200
            );
        }
    }

    //  verify user (web & mobile)
    public function verifyUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users',
            'verificationCode' => 'required|string'
        ]);

        $userData = $request->only(['email', 'verificationCode']);

        $user = UserVerification::where($userData)->first();

        if ($user && $user->verificationCode === $request->verificationCode) {

            $user->verificationCode = null;
            $user->save();

            return response()->json(
                [
                    'success' => 'User verified successfully',
                    'user_id' => $user->id
                ],
                200
            );
        } else {
            return response()->json(
                ['error' => 'Invalid user or veirification code'],
                404
            );
        }
    }
}
