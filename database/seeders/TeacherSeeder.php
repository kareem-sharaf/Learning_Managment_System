<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Teacher;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        teacher::create([
            'id' => '1',
            'name' => 'ahmad',
            'description' => 'asdf',
            'image_data' => '2'

        ]);
        teacher::create([
            'id' => '2',
            'name' => 'khaled',
            'description' => '2asdf',
            'image_data' => '3'
        ]);
        teacher::create([
            'id' => '3',
            'name' => 'soso',
            'description' => '2ffs',
            'image_data' => '3'
        ]);
        teacher::create([
            'id' => '4',
            'name' => 'fofo',
            'description' => '2sdf',
            'image_data' => '1'
        ]);
    }
}
