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
use App\Models\Subject;
use App\Models\Unit;
use App\Models\Lesson;

use App\Mail\EmailVerification;

class AuthController extends Controller
{
    public function updateFcmToken(Request $request)
    {
        // $user = User::find(Auth::user()->id);
        Auth::user()->update(['fcm' => $request->fcm]);

        return response()->json(['message' => 'Updated Successfully']);
    }

    public function registerWeb(Request $request)
{
    $UserVerification = UserVerification::find($request->user_id);

    if (!$UserVerification) {
        return response()->json(['message' => 'User not found'], 404);
    }

    if ($UserVerification->role_id == 4) {
        return response()->json(['message' => 'unauthorized'], 401);
    }

    $request->validate([
        'name' => 'required|string|min:4',
        'password' => [
            'required',
            'regex:/[A-Z]/',
            'regex:/[0-9]/',
            'min:8',
        ],
    ],
    [
        'password.regex' => 'The password must be at least 8 characters long and contain at least one uppercase letter and one number.',
    ]);

    $user = new User([
        'name' => $request->name,
        'email' => $UserVerification->email,
        'password' => Hash::make($request->password),
        'role_id' => $UserVerification->role_id
    ]);

    if ($user->save()) {
        $token = $user->createToken('Personal Access Token')->plainTextToken;
        Auth::login($user, $remember = true);
        return response()->json([
            'message' => 'User logged in successfully',
            'access_token' => $token,
            'user' => $user
        ], 201);
    } else {
        return response()->json(['message' => 'provide proper details'], 422);
    }
}


    //  Create students (mobile);
    public function register(Request $request)
    {
        $key = 'majd123djam321maleh321helam456mm';
        $iv = 'nottonwelbil0990';

        $UserVerification = UserVerification::find($request->user_id);

        if (!$UserVerification) {
            return response()->json(
                ['message' => 'not found'],
                404
            );
        }

        if ($UserVerification->role_id == 1 || $UserVerification->role_id == 2 || $UserVerification->role_id == 3) {
            return response()->json(
                ['message' => 'unauthorized'],
                401
            );
        }

        $request->validate([
            'name' => 'required|string',
            'address_id' => 'required|numeric|unique:users',
            'birth_date' => 'required|date',
            'gender' => 'required',
            'device_id' => 'required|string',
            'image_id' => 'required',
            'year_id' => 'numeric'
        ]);

        $year_id = $request->year_id;
        $stage_id = null;

        if ($year_id) {
            $year = Year::where('id', $year_id)->first();
            $stage_id = $year->stage_id;
        }

        $device_id = openssl_decrypt($request->device_id, 'AES-256-CBC', $key, 0, $iv);

        $user = new User([
            'name' => $request->name,
            'email' => $UserVerification->email,
            'address_id' => $request->address_id,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'device_id' => $device_id,
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

            $key = 'majd123djam321maleh321helam456mm';
            $iv = 'nottonwelbil0990';

            $hashedDeviceId = openssl_decrypt($request->device_id, 'AES-256-CBC', $key, 0, $iv);
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

        if (!$user || $user['role_id'] == 4 || !Hash::check($request->password, $user->password)) {
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

        $length = 7;
        $characters = '00112233445566778899abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $verificationCode = substr(str_shuffle($characters), 0, $length);

        $user->verificationCode = $verificationCode;
        $user->device_id = null;
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

        $length = 7;
        $characters = '00112233445566778899abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $verificationCode = substr(str_shuffle($characters), 0, $length);

        if ($user->verificationCode == null) {
            $user = User::updateOrCreate(
                ['email' => $user->email],
                [
                    'verificationCode' => $verificationCode,
                    'email_sent_at' => $currentTime,
                ]
            );

            Mail::to($user->email)->send(new EmailVerification($verificationCode));

            return response()->json(
                ['message' => 'Verification code sent successfully'],
                200
            );
        } else {

            $previousRequestTime = $user->email_sent_at;

            if ($previousRequestTime && $currentTime->diffInSeconds($previousRequestTime) < 60) {
                return response()->json(
                    ['message' => 'Please wait at least 1 minute before requesting another verification code.'],
                    400
                );
            }

            $user = User::updateOrCreate(
                ['email' => $user->email],
                [
                    'verificationCode' => $verificationCode,
                    'email_sent_at' => $currentTime,
                ]
            );

            Mail::to($user->email)->send(new EmailVerification($verificationCode));

            return response()->json(
                ['message' => 'Verification code sent successfully'],
                200
            );
        }
    }

    //  check user and send code
    public function check_user(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {

            return response()->json(['message' => 'User not found'], 404);
        }

        $length = 7;
        $characters = '00112233445566778899abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $verificationCode = substr(str_shuffle($characters), 0, $length);

        Mail::to($user->email)->send(new EmailVerification($verificationCode));

        $user->verificationCode = $verificationCode;
        $user->save();

        return response()->json(
            ['message' => 'Verification code sent successfully'],
            200
        );
    }

    public function check_code(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'verificationCode' => 'string'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(
                ['message' => 'User not found'],
                404
            );
        }

        if ($request->verificationCode !== $user->verificationCode) {
            return response()->json(
                ['message' => 'Invalid verification code'],
                400
            );
        }

        // $user->verificationCode = null;
        // $user->save();
        return response()->json(
            ['message' => 'Verification code is valid'],
            200
        );
    }

    //  set new password
    public function setPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'newPassword' => 'required|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|min:8',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(
                ['message' => 'User not found.'],
                404
            );
        }

        if ($user->role_id == 4 || $user->verificationCode == null) {
            return response()->json(
                ['message' => 'Unauthorized'],
                400
            );
        }

        if (Hash::check($request->newPassword, $user->password)) {
            return response()->json(
                ['message' => 'New password must be different from old password.'],
                400
            );
        } else {
            $user->password = Hash::make($request->newPassword);
            $user->verificationCode = null;
            $user->save();

            return response()->json(
                ['message' => 'Password reset successfully'],
                200
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
    // index users
    public function indexUsers()
    {
        $currentUser = Auth::user();

        if (!$currentUser) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        if ($currentUser->role_id === 1) {
            $users = User::with(['address', 'role'])
                ->select('name', 'email', 'address_id', 'role_id','id')
                ->get()
                ->map(function ($user) {
                    return [
                        'name' => $user->name,
                        'email' => $user->email,
                        'address' => $user->address->address,
                        'role' => $user->role->role,
                        'id' =>$user->id,
                    ];
                });
        } elseif ($currentUser->role_id === 2) {
            $users = User::with(['address', 'role'])
                ->select('name', 'email', 'address_id', 'role_id','id')
                ->whereIn('role_id', [3, 4])
                ->get()
                ->map(function ($user) {
                    return [
                        'name' => $user->name,
                        'email' => $user->email,
                        'address' => $user->address->address,
                        'role' => $user->role->role,
                        'id' =>$user->id,
                    ];
                });
        } else {
            return response()->json(['status' => 'error', 'message' => 'Forbidden'], 403);
        }

        return response()->json(['status' => 'success', 'data' => $users], 200);
    }


    // delete user
    public function deleteUser(Request $request)
{
    $currentUser = Auth::user();

    if ($currentUser->role_id !== 1) { // التحقق من أن المستخدم الحالي هو مدير
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $request->validate([
        'user_id' => 'required|exists:users,id',
    ]);

    $userId = $request->input('user_id');
    $userToDelete = User::find($userId);

    if (!$userToDelete) {
        return response()->json(['message' => 'User not found'], 404);
    }

    if ($userToDelete->id == $currentUser->id) {
        return response()->json(['message' => 'You cannot delete yourself'], 403);
    }

    switch ($userToDelete->role_id) {
        case 1: // Manager
            $otherManagersCount = User::where('role_id', 1)->where('id', '!=', $userId)->count();
            if ($otherManagersCount > 0) {
                $userToDelete->delete();
                return response()->json(['message' => 'Manager deleted successfully'], 200);
            } else {
                return response()->json(['message' => 'Cannot delete the only remaining manager'], 403);
            }

        case 2: // Admin
            $userToDelete->delete();
            return response()->json(['message' => 'Admin deleted successfully'], 200);

        case 3: // Teacher
            $subjects = $userToDelete->subjects()->get();
            foreach ($subjects as $subject) {
                // Update the exist column for the subject
                $subject->update(['exist' => false]);

                // Update the exist column for related units
                Unit::where('subject_id', $subject->id)
                    ->update(['exist' => false]);

                // Update the exist column for related lessons
                Lesson::whereIn('unit_id', function($query) use ($subject) {
                    $query->select('id')
                        ->from('units')
                        ->where('subject_id', $subject->id);
                })->update(['exist' => false]);
            }

            // Mark the teacher's profile as deleted by updating the email
            $userToDelete->update([
                'email' => 'deleted_user@example.com',
            ]);

            return response()->json(['message' => 'Teacher marked as deleted and related subjects, units, and lessons updated'], 200);

        case 4: // Student
            $userToDelete->delete();
            return response()->json(['message' => 'Student deleted successfully'], 200);

        default:
            return response()->json(['message' => 'Invalid role'], 400);
    }
}



    //     seed users
    public function seedUsers(Request $request)
    {
        Artisan::call('db:seed', ['--class' => 'UserSeeder']);
    }
}
