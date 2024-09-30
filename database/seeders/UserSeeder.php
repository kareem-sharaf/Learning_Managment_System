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
            'image' => 'http://127.0.0.1:8000/category_images/1725098376.jpg',
            'password' => Hash::make('passwordD1'),
            'birth_date' => "2002/3/29",
            'gender' => 1,
            'role_id' => 1,
            'fcm'=>"fdgd",
        ]);
        User::create([
            'name' => "kareem sharaf",
            'email' => "user0@example.com",
            'address_id' => 1,
            'image' => 'http://127.0.0.1:8000/category_images/1725098376.jpg',
            'password' => Hash::make('passwordD1'),
            'birth_date' => "2003/5/24",
            'gender' => 0,
            'role_id' => 2,
            'fcm'=>"fdgds",

        ]);
        User::create([
            'name' => "malek",
            'email' => "user1@example.com",
            'address_id' => 1,
            'image' => 'http://127.0.0.1:8000/category_images/1725098376.jpg',
            'password' => Hash::make('passwordD1'),
            'birth_date' => "2003/5/11",
            'gender' => 0,
            'role_id' => 3,
            'fcm'=>"fdgd",

        ]);
        User::create([
            'name' => "ahmad",
            'email' => "user2@example.com",
            'address_id' => 1,
            'image' => 'http://127.0.0.1:8000/category_images/1725098376.jpg',
            'password' => Hash::make('passwordD1'),
            'birth_date' => "2003/5/11",
            'gender' => 0,
            'role_id' => 3,
            'fcm'=>"fdgd"
,
        ]);
        User::create([
            'name' => "khaled",
            'email' => "user3@example.com",
            'address_id' => 1,
            'image' => 'http://127.0.0.1:8000/category_images/1725098376.jpg',
            'password' => Hash::make('passwordD1'),
            'birth_date' => "2003/5/11",
            'gender' => 0,
            'role_id' => 3,
            'fcm'=>"fdgd"
,
        ]);
        User::create([
            'name' => "samer",
            'email' => "user4@example.com",
            'address_id' => 1,
            'image' => 'http://127.0.0.1:8000/category_images/1725098376.jpg',
            'password' => Hash::make('passwordD1'),
            'birth_date' => "2003/5/11",
            'gender' => 0,
            'role_id' => 3,
            'fcm'=>"fdgd"
,
        ]);
        User::create([
            'name' => "jojo",
            'email' => "user5@example.com",
            'address_id' => 1,
            'image' => 'http://127.0.0.1:8000/category_images/1725098376.jpg',
            'password' => Hash::make('passwordD1'),
            'birth_date' => "2003/5/11",
            'gender' => 0,
            'role_id' => 3,
            'fcm'=>"fdgd"
,
        ]);
        User::create([
            'name' => "parhom",
            'email' => "user6@example.com",
            'address_id' => 1,
            'image' => 'http://127.0.0.1:8000/category_images/1725098376.jpg',
            'password' => Hash::make('passwordD1'),
            'birth_date' => "2003/5/11",
            'gender' => 0,
            'role_id' => 3,
            'fcm'=>"fdgd"
,

        ]);
        User::create([
            'name' => "anas",
            'email' => "user7@example.com",
            'address_id' => 1,
            'image' => 'http://127.0.0.1:8000/category_images/1725098376.jpg',
            'password' => Hash::make('passwordD1'),
            'birth_date' => "2003/5/11",
            'gender' => 0,
            'role_id' => 4,
            'fcm'=>"fdgd"
,
            'balance' => 500000,

        ]);
        User::create([
            'name' => "aya",
            'email' => "user8@example.com",
            'address_id' => 1,
            'image' => 'http://127.0.0.1:8000/category_images/1725098376.jpg',
            'password' => Hash::make('passwordD1'),
            'birth_date' => "2003/5/11",
            'device_id' => 'mmm9',
            'gender' => 0,
            'role_id' => 4,
            'fcm'=>"fdgd"
,
            'balance' => 500000,

        ]);
        User::create([
            'name' => "saaaa",
            'email' => "user9@example.com",
            'address_id' => 1,
            'image' => 'http://127.0.0.1:8000/category_images/1725098376.jpg',
            'password' => Hash::make('passwordD1'),
            'birth_date' => "2003/5/11",
            'gender' => 0,
            'role_id' => 4,
            'fcm'=>"fdgd"
,
            'balance' => 500000,

        ]);
        User::create([
            'name' => "mooo",
            'email' => "user10@example.com",
            'address_id' => 1,
            'image' => 'http://127.0.0.1:8000/category_images/1725098376.jpg',
            'password' => Hash::make('passwordD1'),
            'birth_date' => "2003/5/11",
            'gender' => 0,
            'role_id' => 4,
            'fcm'=>"fdgd"
,
            'balance' => 500000,

        ]);
        User::create([
            'name' => "booo",
            'email' => "user11@example.com",
            'address_id' => 1,
            'image' => 'http://127.0.0.1:8000/category_images/1725098376.jpg',
            'password' => Hash::make('passwordD1'),
            'birth_date' => "2003/5/11",
            'gender' => 0,
            'role_id' => 4,
            'fcm'=>"fdgd"
,
            'balance' => 500000,

        ]);
    }
}
