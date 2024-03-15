<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Support\Facades\Artisan;

class RoleController extends Controller
{
    //  index all roles
    public function index(){
        $roles = Role::all();
        return response()->json([$roles]);
    }

    //  seed roles
    public function seedRoles(){
        Artisan::call('db:seed', ['--class' => 'RoleSeeder']);
    }

    //  update role
    public function update(){
        
    }
}
