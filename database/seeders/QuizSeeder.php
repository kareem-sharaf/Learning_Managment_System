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
        // Assuming you have already created some subjects, units, and lessons
        $subject = Subject::first();
        $unit = Unit::first();
        $lesson = Lesson::first();

        Quiz::create([
            'name' => 'Quiz for Subject',
            'duration' => '60',
            'total_mark' => 100,
            'success_mark' => 50,
            'public' => true,
            'type_id' => 2,
            'type_type' => 'App\Models\Subject',
            'teacher_id' => 3
        ]);
        Quiz::create([
            'name' => 'Quiz for Subject',
            'duration' => '60',
            'total_mark' => 100,
            'success_mark' => 50,
            'public' => false,
            'type_id' => 2,
            'type_type' => 'App\Models\Subject',
            'teacher_id' => 3
        ]);
        Quiz::create([
            'name' => 'Quiz for Unit',
            'duration' => '60',
            'total_mark' => 100,
            'success_mark' => 50,
            'public' => true,
            'type_id' => 1,
            'type_type' => 'App\Models\Unit',
            'teacher_id' => 1
        ]);
        Quiz::create([
            'name' => 'Quiz for Unit',
            'duration' => '60',
            'total_mark' => 100,
            'success_mark' => 50,
            'public' => false,
            'type_id' => 1,
            'type_type' => 'App\Models\Unit',
            'teacher_id' => 1
        ]);
        Quiz::create([
            'name' => 'Quiz for Lesson',
            'duration' => '60',
            'total_mark' => 100,
            'success_mark' => 50,
            'public' => true,
            'type_id' => 2,
            'type_type' => 'App\Models\Lesson',
            'teacher_id' => 1
        ]);
        Quiz::create([
            'name' => 'Quiz for Lesson',
            'duration' => '60',
            'total_mark' => 100,
            'success_mark' => 50,
            'public' => false,
            'type_id' => 2,
            'type_type' => 'App\Models\Lesson',
            'teacher_id' => 1
        ]);
    }
}
