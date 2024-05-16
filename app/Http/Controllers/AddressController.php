<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class AddressController extends Controller
{
    //  seed addresse
    public function seedAddress()
    {
        Artisan::call('db:seed', ['--class' => 'AddressSeeder']);
    }
    
    //  index all roles
    public function index()
    {
        $Addresses = Address::all();
        if ($Addresses) {
            return response()->json(
                ['Addresses' => $Addresses],
                200
            );
        }
        return response()->json(
            ['message' => 'no address found'],
            404
        );
    }
}
