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
            'name' => 'English',
            'description' => 'a+',
            'category_id' => '2',
        ]);

        Subject::create([
            'name' => 'c++',
            'description' => 'a+',
            'category_id' => '3',
        ]);

        Subject::create([
            'name' => 'python',
            'description' => 'b+',
            'category_id' => '3',
        ]);

        Subject::create([
            'name' => 'maths',
            'description' => 'numbers',
            'category_id' => '1',
        ]);

        Subject::create([
            'name' => 'physics',
            'description' => 'mc',
            'category_id' => '1',
        ]);
        Subject::create([
            'name' => 'chemistry',
            'description' => 'br',
            'category_id' => '1',
        ]);
        Subject::create([
            'name' => 'java',
            'description' => 'b+',
            'category_id' => '3',
        ]);
        Subject::create([
            'name' => 'Arabic',
            'description' => 'char',
            'category_id' => '2',
        ]);
    }
}
