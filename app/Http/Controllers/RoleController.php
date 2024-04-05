<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    //  seed roles
    public function seedRoles()
    {
        Artisan::call('db:seed', ['--class' => 'RoleSeeder']);
    }

    //  index all roles
    public function index()
    {
        $roles = Role::all();
        if ($roles) {
            return response()->json(
                ['roles' => $roles],
                200
            );
        }
        return response()->json(
            ['message' => 'no roles found'],
            404
        );
    }

    //  index roles for web
    public function index_web()
    {
        $roles = Role::take(3)->get();

        if ($roles->isEmpty()) {
            return response()->json(['message' => 'No roles found'], 404);
        }

        return response()->json(['roles' => $roles], 200);
    }

    //  store new role
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'role' => 'required|string|unique:roles|max:255',
        ]);

        $role = new Role();
        $role->role = $request->role;

        if ($role->save()) {
            return response()->json(
                ['message' => 'Role created successfully', 'role' => $role],
                201
            );
        } else {
            return response()->json(
                ['message' => 'Failed to create role'],
                500
            );
        }
    }


    /*  allow the user with the manager role to have the
         ability to promote or demote another user  */
    public function update(Request $request)
    {
        $user = Auth::user();

        if ($user->role_id == 1 || ($user->role_id == 2 && $request->role_id == 2)) {
            $updated_user = User::where('email', $request->email)
                ->first();

            if (!$updated_user) {
                return response()->json(
                    ['message' => 'user not found!'],
                    404
                );
            }

            $updated_user->role_id = $request->new_role_id;
            $updated_user->save();

            return response()->json(
                ['message' => 'user role updated succesfully!'],
                200
            );
        }
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    public function destroy(Request $request)
    {
        $user = Auth::user();

        $role = Role::where('role', $request->role)
            ->first();
        if ($role) {
            $role->delete();
            return response()->json(
                ['message' => 'Successfully deleted role!'],
                200
            );
        }
        return response()->json(
            ['message' => 'role not found!'],
            404
        );
    }
}
