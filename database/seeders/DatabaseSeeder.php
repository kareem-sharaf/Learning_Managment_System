<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            StageSeeder::class,
            YearSeeder::class,
            AddressSeeder::class,
            UserSeeder::class,
            SubjectSeeder::class,
            CategorySeeder::class,
            Teacher_subject_yearSeeder::class,
            UnitsSeeder::class,
            LessonsSeeder::class,
            CommentSeeder::class,
            VideoSeeder::class,
            FilesSeeder::class,
            MessageSeeder::class,
            QuizSeeder::class,
            QuestionSeeder::class,
            ProgressSeeder::class,
            StudentExamsSeeder::class,
            SubscriptionSeeder::class,
            ADSeeder::class,

                ]);
    }
}

