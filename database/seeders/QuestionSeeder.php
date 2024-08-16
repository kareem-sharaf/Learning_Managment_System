<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\Quiz;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all quizzes
        $quizzes = Quiz::all();

        foreach ($quizzes as $quiz) {
            for ($i = 1; $i <= 5; $i++) {
                Question::create([
                    'text' => 'Question ' . $i . ' for ' . $quiz->name,
                    'mark' => 10,
                    'answers' => json_encode(['Option A', 'Option B', 'Option C', 'Option D']),
                    'correct_answer' => 'Option A',
                    'quiz_id' => $quiz->id,
                ]);
            }
        }
    }
}
