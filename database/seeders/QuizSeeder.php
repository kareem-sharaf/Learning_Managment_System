<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Quiz;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\Lesson;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Loop through all subjects and create quizzes
        $subjects = Subject::all();
        foreach ($subjects as $subject) {
            Quiz::create([
                'name' => 'Public Quiz for ' . $subject->name,
                'duration' => '60',
                'total_mark' => 100,
                'success_mark' => 50,
                'public' => true,
                'type_id' => $subject->id,
                'type_type' => 'App\Models\Subject',
                'teacher_id' => 3, // Adjust teacher_id as necessary
            ]);

            Quiz::create([
                'name' => 'Private Quiz for ' . $subject->name,
                'duration' => '60',
                'total_mark' => 100,
                'success_mark' => 50,
                'public' => false,
                'type_id' => $subject->id,
                'type_type' => 'App\Models\Subject',
                'teacher_id' => 3, // Adjust teacher_id as necessary
            ]);
        }

        // Loop through all units and create quizzes
        $units = Unit::all();
        foreach ($units as $unit) {
            Quiz::create([
                'name' => 'Public Quiz for ' . $unit->name,
                'duration' => '60',
                'total_mark' => 100,
                'success_mark' => 50,
                'public' => true,
                'type_id' => $unit->id,
                'type_type' => 'App\Models\Unit',
                'teacher_id' => 1, // Adjust teacher_id as necessary
            ]);

            Quiz::create([
                'name' => 'Private Quiz for ' . $unit->name,
                'duration' => '60',
                'total_mark' => 100,
                'success_mark' => 50,
                'public' => false,
                'type_id' => $unit->id,
                'type_type' => 'App\Models\Unit',
                'teacher_id' => 1, // Adjust teacher_id as necessary
            ]);
        }

        // Loop through all lessons and create quizzes
        $lessons = Lesson::all();
        foreach ($lessons as $lesson) {
            Quiz::create([
                'name' => 'Public Quiz for ' . $lesson->name,
                'duration' => '60',
                'total_mark' => 100,
                'success_mark' => 50,
                'public' => true,
                'type_id' => $lesson->id,
                'type_type' => 'App\Models\Lesson',
                'teacher_id' => 1, // Adjust teacher_id as necessary
            ]);

            Quiz::create([
                'name' => 'Private Quiz for ' . $lesson->name,
                'duration' => '60',
                'total_mark' => 100,
                'success_mark' => 50,
                'public' => false,
                'type_id' => $lesson->id,
                'type_type' => 'App\Models\Lesson',
                'teacher_id' => 1, // Adjust teacher_id as necessary
            ]);
        }
    }
}
