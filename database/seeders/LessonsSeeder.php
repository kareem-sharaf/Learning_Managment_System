<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Lesson;

class LessonsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Lesson::create([
            'id' => '1',
            'name' => 'part 1',
            'unit_id' => '2',
            'price' => '2',
            'description' => '2',
            'image' => '2',
            'teacher_id' => '3'

        ]);
        Lesson::create([
            'id' => '2',
            'name' => 'prprprprprprp',
            'unit_id' => '1',
            'price' => '2',
            'description' => '2',
            'image' => '2',
            'teacher_id' => '3'

        ]);
    }
}
