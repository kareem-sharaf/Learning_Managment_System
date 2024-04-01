<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;
use App\Models\UserVerification;
use App\Models\User;
use App\Models\Year;
use App\Models\Address;
use App\Models\Stage;
use App\Mail\EmailVerification;

class AuthController extends Controller
{
    public function registerWeb(Request $request)
    {
        $UserVerification = UserVerification::find($request->user_id);

        if ($UserVerification->role_id == 4) {
            return response()->json(
                ['message' => 'unauthorized'],
                401
            );
        }

        if ($UserVerification->verified == 0) {
            return response()->json(
                ['message' => 'not verified'],
                401
            );
        }

        $request->validate([
            'name' => 'required|string|min:4',
            'password' => 'required|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|min:8',
            'address_id' => 'required',
            'birth_date' => 'required|date',
            'gender' => 'required',
            'image_id' => 'required|numeric'
        ]);
        $user = new User([
            'name' => $request->name,
            'email' => $UserVerification->email,
            'address_id' => $request->address_id,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'verified' => 1,
            'image_id' => $request->image_id,
            'password' => Hash::make($request->password),
            'role_id' => $UserVerification->role_id
        ]);

        if ($user->save()) {
            $token = $user->createToken('Personal Access Token')->plainTextToken;
            Auth::login($user, $remember = true);
            return response()->json(
                [
                    'message' => 'User logged in successfully',
                    'access_token' => $token,
                    'user' => $user
                ],
                201
            );
        } else {
            return response()->json(
                ['message' => 'provide proper details'],
                422
            );
        }
    }

    //  Create students (mobile);
    public function register(Request $request)
    {
        $UserVerification = UserVerification::find($request->user_id);

        if ($UserVerification->role_id == 1 || $UserVerification->role_id == 2 || $UserVerification->role_id == 3) {
            return response()->json(
                ['message' => 'unauthorized'],
                401
            );
        }

        if ($UserVerification->verified == 0) {
            return response()->json(
                ['message' => 'not verified'],
                401
            );
        }

        $request->validate([
            'name' => 'required|string',
            'address_id' => 'required|numeric',
            'birth_date' => 'required|date',
            'gender' => 'required',
            'device_id' => 'required|string',
            'image_id' => 'required',
            'year_id' => 'numeric'
        ]);

        $length = 7;
        $characters = '00112233445566778899abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $verificationCode = substr(str_shuffle($characters), 0, $length);

        $year_id = $request->year_id;
        $stage_id = null;

        if ($year_id) {
            $year = Year::where('id', $year_id)->first();
            $stage_id = $year->stage_id;
        }

        $user = new User([
            'name' => $request->name,
            'email' => $UserVerification->email,
            'address_id' => $request->address_id,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'verified' => 1,
            'device_id' => hash('sha512', $request->device_id),
            'image_id' => $request->image_id,
            'role_id' => $UserVerification->role_id,
            'year_id' => $request->year_id,
            'stage_id' => $stage_id
        ]);

        $UserVerification->delete();

        if ($user->save()) {
            $token = $user->createToken('Personal Access Token')->plainTextToken;
            Auth::login($user, $remember = true);
            return response()->json(
                [
                    'message' => 'User logged in successfully',
                    'access_token' => $token,
                    'user' => $user
                ],
                201
            );
        } else {
            return response()->json(
                ['message' => 'provide proper details'],
                422
            );
        }
    }

    // login students (mobile)
    public function login(Request $request)
    {
        $request->validate([
            'device_id' => 'required|string',
            'verificationCode' => 'string|sometimes'
        ]);

        $user = null;

        if ($request->has('verificationCode')) {
            $user = User::where('verificationCode', $request->verificationCode)->first();

            if (!$user) {
                return response()->json(['message' => 'User not found.'], 404);
            }

            if ($request->verificationCode != $user->verificationCode) {
                return response()->json(['message' => 'Invalid verification code.'], 400);
            }

            $user->device_id = $request->device_id;
            $user->verificationCode = null;
            $user->save();
        } else {

            $hashedDeviceId = hash('sha512', $request->device_id);
            $user = User::where('device_id', $hashedDeviceId)->first();


            if (!$user) {
                return response()->json(['message' => 'Please sign up before logging in.'], 401);
            }

            if ($user->role_id == 1 || $user->role_id == 2 || $user->role_id == 3) {
                return response()->json(
                    ['message' => 'unauthorized'],
                    400
                );
            }

            if ($user->verified == 0) {
                return response()->json(['message' => 'User not verified.'], 401);
            }
        }

        $token = $user->createToken('Personal Access Token')->plainTextToken;

        Auth::login($user, $remember = true);

        return response()->json(
            [
                'message' => 'User logged in successfully',
                'access_token' => $token,
                'user' => $user
            ],
            201
        );
    }

    //  login web
    public function loginWeb(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->verified == 0) {
            return response()->json(
                ['message' => 'not verified'],
                401
            );
        }

        if (!$user || $user['role'] == 4 || !Hash::check($request->password, $user->password)) {
            return response()->json(
                ['message' => 'unauthenticated'],
                400
            );
        }

        $token = $user->createToken('Personal Access Token')->plainTextToken;

        Auth::login($user, $remember = true);

        return response()->json(
            [
                'message' => 'User logged in successfully',
                'access_token' => $token,
                'user' => $user
            ],
            201
        );
    }

    //  send verification code to reset device_id
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->verified == 0) {
            return response()->json(
                ['message' => 'not verified'],
                401
            );
        }

        $length = 7;
        $characters = '00112233445566778899abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $verificationCode = substr(str_shuffle($characters), 0, $length);

        $user->verificationCode = $verificationCode;
        $user->verified = 0;
        $user->save();

        Mail::to($user->email)->send(new EmailVerification($verificationCode));

        return response()->json(
            ['message' => 'Verification code sent successfully'],
            200
        );
    }

    // resend email
    public function resendEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

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

        $user = User::updateOrCreate(
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

    //  set new password
    public function setPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'verificationCode' => 'required|string',
            'newPassword' => 'required|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|min:8',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        if ($user->role_id = 4) {
            return response()->json(
                ['message' => 'unauthorized'],
                400
            );
        }

        if (Hash::check($request->newPassword, $user->password)) {
            return response()->json(['message' => 'New password must be different from old password.'], 400);
        }

        if ($request->verificationCode == $user->verificationCode) {
            $user->password = Hash::make($request->newPassword);
            $user->verificationCode = null;
            $user->save();

            return response()->json(
                ['message' => 'Password reset successfully'],
                200
            );
        } else {
            return response()->json(
                ['message' => 'Invalid verification code'],
                400
            );
        }
    }

    //  Auth requirments
    public function indexAddressYears()
    {
        $Addresses = Address::all();
        $years = Year::all();
        return response()->json(
            ['years' => $years, 'addresses' => $Addresses],
            200
        );
    }

    //   logout
    public function logout(Request $request)
    {
        $user = Auth::user();
        $request->user()->tokens()->delete();
        return response()->json(
            ['message' => 'Successfully logged out']
        );
    }

    //     seed users
    public function seedUsers(Request $request)
    {
        Artisan::call('db:seed', ['--class' => 'UserSeeder']);
    }
}
