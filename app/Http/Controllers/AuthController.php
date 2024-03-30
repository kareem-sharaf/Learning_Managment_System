<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
                ['error' => 'unauthorized'],
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
            'image_id' => $request->image_id,
            'password' => Hash::make($request->password),
            'role_id' => $UserVerification->role_id
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

    //  Create students (mobile);
    public function register(Request $request)
    {
        $UserVerification = UserVerification::find($request->user_id);
        if ($UserVerification->role_id == 1 || $UserVerification->role_id == 2 || $UserVerification->role_id == 3) {
            return response()->json(
                ['error' => 'unauthorized'],
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
            'device_id' => $request->device_id,
            'image_id' => $request->image_id,
            'role_id' => $UserVerification->role_id,
            'year_id' => $request->year_id,
            'stage_id' => $stage_id
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


    // login students (mobile)
    public function login(Request $request)
    {
        $request->validate([
            'device_id' => 'required|string'
        ]);

        $user = User::where('device_id', $request->device_id)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        if ($user->device_id !== $request->device_id) {
            $user->device_id = $request->device_id;
            $user->save();
        }

        $token = $user->createToken('Personal Access Token')->plainTextToken;
        Auth::login($user, $remember = true);
        return response()->json(
            ['accessToken' => $token],
            200
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

        if (!$user || $user['role'] == 4 || !Hash::check($request->password, $user->password)) {
            return response()->json(
                ['error' => 'unauthenticated'],
                400
            );
        }

        $token = $user->createToken('Personal Access Token')->plainTextToken;
        Auth::login($user, $remember = true);
        return response()->json(
            ['success' => 'user logged in successfuly', 'access token' => $token],
            201
        );
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
