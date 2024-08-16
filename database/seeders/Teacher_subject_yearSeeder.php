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
        // English - Teacher: malek
        TeacherSubjectYear::create([
            'id' => '1',
            'user_id' => '3', // المعلم "malek"
            'subject_id' => '1', // المادة "English"
        ]);

        // C++ - Teacher: ahmad
        TeacherSubjectYear::create([
            'id' => '2',
            'user_id' => '4', // المعلم "ahmad"
            'subject_id' => '2', // المادة "C++"
        ]);

        // Python - Teacher: khaled
        TeacherSubjectYear::create([
            'id' => '3',
            'user_id' => '5', // المعلم "khaled"
            'subject_id' => '3', // المادة "Python"
        ]);

        // Maths - Teacher: samer
        TeacherSubjectYear::create([
            'id' => '4',
            'user_id' => '6', // المعلم "samer"
            'subject_id' => '4', // المادة "Maths"
            'year_id' => '10' // السنة "1st-sci"
        ]);

        // Physics - Teacher: jojo
        TeacherSubjectYear::create([
            'id' => '5',
            'user_id' => '7', // المعلم "jojo"
            'subject_id' => '5', // المادة "Physics"
            'year_id' => '11' // السنة "2nd-sci"
        ]);

        // Chemistry - Teacher: parhom
        TeacherSubjectYear::create([
            'id' => '6',
            'user_id' => '8', // المعلم "parhom"
            'subject_id' => '6', // المادة "Chemistry"
            'year_id' => '12' // السنة "3rd-sci"
        ]);

        // Java - Teacher: anas
        TeacherSubjectYear::create([
            'id' => '7',
            'user_id' => '9', // المعلم "anas"
            'subject_id' => '7', // المادة "Java"
        ]);

        // Arabic - Teacher: aya
        TeacherSubjectYear::create([
            'id' => '8',
            'user_id' => '10', // المعلم "aya"
            'subject_id' => '8', // المادة "Arabic"
        ]);
    }
}
