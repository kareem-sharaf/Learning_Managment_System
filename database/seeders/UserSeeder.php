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
            'email' => "user@example.com",
            'address_id' => 1,
            'image_id' => 1,
            'password' => Hash::make('password'),
            'birth_date' => "2002/3/29",
            'gender' => 1,
            'role_id' => 1,
            'verified' => 1
        ]);
        User::create([
            'name' => "kareem sharaf",
            'email' => "user0@example.com",
            'address_id' => 1,
            'image_id' => 1,
            'password' => Hash::make('password'),
            'birth_date' => "2003/5/24",
            'gender' => 0,
            'role_id' => 2,
            'verified' => 1

        ]);
        User::create([
            'name' => "malek al-imam",
            'email' => "user1@example.com",
            'address_id' => 1,
            'image_id' => 1,
            'password' => Hash::make('password'),
            'birth_date' => "2003/5/11",
            'gender' => 0,
            'role_id' => 3,
            'verified' => 1

        ]);
    }
}
