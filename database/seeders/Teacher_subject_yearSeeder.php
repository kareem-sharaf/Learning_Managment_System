<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TeacherSubjectYear;

class Teacher_subject_yearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TeacherSubjectYear::create([
            'id' => '1',
            'user_id' => '3',
            'subject_id' => '1',
            'year_id' => null
        ]);
        TeacherSubjectYear::create([
            'id' => '2',
            'user_id' => '4',
            'subject_id' => '2',
            'year_id' => null
        ]);
        TeacherSubjectYear::create([
            'id' => '3',
            'user_id' => '5',
            'subject_id' => '3',
            'year_id' => null
        ]);
        TeacherSubjectYear::create([
            'id' => '4',
            'user_id' => '6',
            'subject_id' => '4',
            'year_id' => '7'
        ]);
        TeacherSubjectYear::create([
            'id' => '5',
            'user_id' => '7',
            'subject_id' => '5',
            'year_id' => '8'
        ]);
    }
}
