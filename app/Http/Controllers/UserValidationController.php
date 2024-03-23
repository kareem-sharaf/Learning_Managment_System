<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\UserValidation;
use App\Models\User;


class UserValidationController extends Controller
{

    //  create a user and generate a random code for verfication
    public function createUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'father_name' => 'required|string',
            'phone_number' => 'required|numeric|unique:user_validations',
            'role_id' => 'required|numeric',
        ]);

        $length = 7;
        $characters = '00112233445566778899abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $validation_code = substr(str_shuffle($characters), 0, $length);

        $validatedUser = new UserValidation([
            'name' => $request->name,
            'father_name' => $request->father_name,
            'phone_number' => $request->phone_number,
            'role_id' => $request->role_id,
            'validation_code' => $validation_code
        ]);

        if ($validatedUser->save()) {
            return response()->json(
                [
                    'message' => 'successfully created a user',
                    'validation_code' => $validation_code
                ],
                200
            );
        }
    }

    //  login user (web)
    public function validateUser(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|numeric',
            'validation_code' => 'required|string'
        ]);

        $userData = $request->only(['phone_number', 'validation_code']);

        $user = UserValidation::where($userData)->first();

        if ($user && $user->validation_code === $request->validation_code) {

            $user->validation_code = null;
            $user->save();

            return response()->json(
                [
                    'message' => 'User validated successfully',
                    'user' => $user
                ],
                200
            );
        } else {
            return response()->json([
                'message' => 'Invalid user or validation code',
            ], 404);
        }
    }

    public function setupUser(Request $request)
    {
        $userValidation = UserValidation::find($request->user_id);
        $request->validate([
            'password' => 'required|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|min:8',
            'email' => 'email|unique:users',
            'address_id' => 'required',
            'birth_date' => 'required|date'
        ]);
        $user = new User([
            'name' => $userValidation->name,
            'father_name' => $userValidation->father_name,
            'phone_number' => $userValidation->phone_number,
            'password' => Hash::make($request->password),
            'email' => $request->email,
            'address_id' => $request->address_id,
            'birth_date' => $request->birth_date,
            'image_id' => $request->image_id,
            'role_id' => $userValidation->role_id
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
}
