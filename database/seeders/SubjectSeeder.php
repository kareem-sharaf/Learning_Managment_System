<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Subject;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Subject::create([
            'id' => '1',
            'name' => 'physics',
            'class_id' => 1
        ]);
        Subject::create([
            'id' => '2',
            'name' => 'chemistry',
            'class_id' => 1
        ]);
        Subject::create([
            'id' => '3',
            'name' => 'programming',
            'class_id' => 3
        ]);
        Subject::create([
            'id' => '4',
            'name' => 'maths',
            'class_id' => 1
        ]);
    }
}
