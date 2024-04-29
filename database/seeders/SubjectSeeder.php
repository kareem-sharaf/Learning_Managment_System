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
            'description' => 'asdf',
            'category_id' => '2',
            'year_id' => '7'

        ]);
        Subject::create([
            'id' => '2',
            'name' => 'chemistry',
            'description' => '2asdf',
            'category_id' => '3',
            'year_id' => '7'

        ]);
        Subject::create([
            'id' => '3',
            'name' => 'programming',
            'description' => '2ffs',
            'category_id' => '3',
            'year_id' => '11'

        ]);
        Subject::create([
            'id' => '4',
            'name' => 'maths',
            'description' => '2sdf',
            'category_id' => '1',
            'year_id' => '3'

        ]);
    }
}
