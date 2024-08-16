<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Subject;
use Illuminate\Support\Arr;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // استرجاع الطلاب الذين لديهم role_id = 4
        $students = User::where('role_id', 4)->pluck('id')->toArray();

        // استرجاع المواد
        $subjects = Subject::pluck('id')->toArray();

        // استرجاع المدرسين الذين لديهم role_id = 3
        $teachers = User::where('role_id', 3)->pluck('id')->toArray();

        // التحقق من وجود بيانات
        if (empty($students) || empty($subjects) || empty($teachers)) {
            return; // إذا لم تكن هناك بيانات، لا تقم بعمل شيء
        }

        // تعبئة سجلات الاشتراكات
        foreach ($students as $studentId) {
            foreach ($subjects as $subjectId) {
                // اختيار مدرس عشوائي
                $teacherId = Arr::random($teachers);

                // تعيين حالة عشوائية: 1 (نشط) أو 0 (غير نشط)
                $status = rand(0, 1);

                Subscription::create([
                    'user_id' => $studentId,
                    'subject_id' => $subjectId,
                    'teacher_id' => $teacherId,
                    'status' => $status,
                ]);
            }
        }
    }
}
