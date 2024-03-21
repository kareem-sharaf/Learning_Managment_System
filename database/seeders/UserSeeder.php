<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => "masa al-zaeem",
            'father_name' => "eyad",
            'phone_number' => "0999999999",
            'password' => Hash::make("password"),
            'email' => "user@example.com",
            'address' => "Address",
            'device_id' => "knafijdskfm",
            'role_id' => 1,
        ]);
        User::create([
            'name' => "kareem sharaf",
            'father_name' => "Al_Zaeem",
            'phone_number' => "0985384953",
            'password' => Hash::make("password"),
            'email' => "user0@example.com",
            'address' => "Address",
            'device_id' => "dfgdHtrazth",
            'role_id' => 2,
        ]);
        User::create([
            'name' => "malek al-imam",
            'father_name' => "Al_kahtib",
            'phone_number' => "0999888777",
            'password' => Hash::make("password"),
            'email' => "user1@example.com",
            'address' => "Address",
            'device_id' => "sdxaferhg",
            'role_id' => 3,
        ]);
    }
}
