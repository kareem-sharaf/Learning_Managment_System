<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'id' => '1',
            'role' => 'manager'
        ]);
        Role::create([
            'id' => '2',
            'role' => 'admin'
        ]);
        Role::create([
            'id' => '3',
            'role' => 'teacher'
        ]);
        Role::create([
            'id' => '4',
            'role' => 'student'
        ]);
    }
}
