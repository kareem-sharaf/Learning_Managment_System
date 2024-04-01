<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
            'email' => 'required|email',
            'role_id' => 'required|numeric'
        ]);

        $existingUserVer = UserVerification::where('email', $request->email)->first();
        $existingUser = UserVerification::where('email', $request->email)->first();

        if ($existingUser && $existingUserVer->verified == 1) {
            return response()->json(['message' => 'Email is already taken'], 400);
        }

        $length = 7;
        $characters = '00112233445566778899abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $verificationCode = substr(str_shuffle($characters), 0, $length);

        if ($user->role_id == 1 || ($user->role_id == 2 && $request->role_id == 2)) {
            $newUser = new UserVerification([
                'email' => $request->email,
                'role_id' => $request->role_id,
                'verificationCode' => $verificationCode,
                'email_sent_at' => Carbon::now()
            ]);

            if ($newUser->save()) {
                Mail::to($newUser->email)->send(new EmailVerification($verificationCode));
                return response()->json(
                    [
                        'message' => 'Successfully created user!',
                        'status' => 'Waiting for verification'
                    ],
                    200
                );
            }
        } else {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }

    //  create users (mobile) and generate a random code for verfication
    public function createUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $existingUser = UserVerification::where('email', $request->email)->first();

        if ($existingUser) {
            return response()->json(['message' => 'Email is already taken'], 400);
        }

        $length = 7;
        $characters = '00112233445566778899abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $verificationCode = substr(str_shuffle($characters), 0, $length);

        $user = new UserVerification([
            'email' => $request->email,
            'role_id' => 4,
            'verificationCode' => $verificationCode,
            'email_sent_at' => Carbon::now()
        ]);

        if ($user->save()) {
            Mail::to($user->email)->send(new EmailVerification($verificationCode));
            return response()->json(
                [
                    'message' => 'Successfully created user!',
                    'status' => 'Waiting for verification'
                ],
                200
            );
        }
    }

    //  verify user (web & mobile)
    public function verifyUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'verificationCode' => 'required|string'
        ]);

        $userData = $request->only(['email', 'verificationCode']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {

            $user = UserVerification::where($userData)->first();
        }

        if ($user && $user->verificationCode === $request->verificationCode) {
            
            $user->verificationCode = null;
            $user->verified = 1;
            $user->save();

            return response()->json(
                [
                    'message' => 'User verified successfully',
                    'user_id' => $user->id
                ],
                200
            );
        } else {
            return response()->json(
                ['message' => 'Invalid user or verification code'],
                404
            );
        }
    }


    // resend email
    public function resend_email(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = UserVerification::where('email', $request->email)->first();

        $currentTime = Carbon::now();
        $previousRequestTime = $user->email_sent_at;

        if ($previousRequestTime && $currentTime->diffInSeconds($previousRequestTime) < 60) {
            return response()->json(
                ['message' => 'Please wait at least 1 minute before requesting another verification code.'],
                400
            );
        }

        $length = 7;
        $characters = '00112233445566778899abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $verificationCode = substr(str_shuffle($characters), 0, $length);

        $user = UserVerification::updateOrCreate(
            ['email' => $user->email],
            [
                'verificationCode' => $verificationCode,
                'email_sent_at' => $currentTime,
                'verified' => 0
            ]
        );

        Mail::to($user->email)->send(new EmailVerification($verificationCode));

        return response()->json(
            ['message' => 'Verification code sent successfully'],
            200
        );
    }
}
