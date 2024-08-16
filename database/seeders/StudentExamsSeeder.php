<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StudentExam;
use App\Models\User;
use App\Models\Quiz;
use Carbon\Carbon;

class StudentExamsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // استرجاع الطلاب الذين لديهم role_id = 4 (طلاب)
        $students = User::where('role_id', 4)->get();

        // استرجاع جميع الكويزات
        $quizzes = Quiz::all();

        // تاريخ اليوم الحالي
        $currentDate = Carbon::now();

        // تعبئة سجلات الامتحانات
        foreach ($students as $student) {
            foreach ($quizzes as $quiz) {
                StudentExam::create([
                    'user_id' => $student->id,
                    'quiz_id' => $quiz->id,
                    'mark' => rand(0, 100), // علامات عشوائية بين 0 و 100
                    'status' => rand(0, 1), // حالة عشوائية بين 0 و 1
                    'date' => $currentDate,
                ]);
            }
        }
    }
}
