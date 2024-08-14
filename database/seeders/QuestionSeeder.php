<?php
namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Question;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = [
            ['text' => 'What is the capital of France?', 'mark' => 10, 'answers' => json_encode(['Paris', 'London', 'Berlin', 'Madrid']), 'correct_answer' => 'Paris', 'quiz_id' => 1],
            ['text' => 'What is the largest planet in our solar system?', 'mark' => 10, 'answers' => json_encode(['Earth', 'Mars', 'Jupiter', 'Saturn']), 'correct_answer' => 'Jupiter', 'quiz_id' => 1],
            ['text' => 'What is the smallest unit of matter?', 'mark' => 10, 'answers' => json_encode(['Atom', 'Molecule', 'Electron', 'Proton']), 'correct_answer' => 'Atom', 'quiz_id' => 1],
            ['text' => 'What is the boiling point of water?', 'mark' => 10, 'answers' => json_encode(['90°C', '100°C', '110°C', '120°C']), 'correct_answer' => '100°C', 'quiz_id' => 1],
            ['text' => 'What is the speed of light?', 'mark' => 10, 'answers' => json_encode(['300,000 km/s', '150,000 km/s', '200,000 km/s', '100,000 km/s']), 'correct_answer' => '300,000 km/s', 'quiz_id' => 1],
            ['text' => 'Who wrote "To be, or not to be"?', 'mark' => 10, 'answers' => json_encode(['Shakespeare', 'Hemingway', 'Tolkien', 'Orwell']), 'correct_answer' => 'Shakespeare', 'quiz_id' => 1],
            ['text' => 'What is the currency of Japan?', 'mark' => 10, 'answers' => json_encode(['Yen', 'Dollar', 'Euro', 'Pound']), 'correct_answer' => 'Yen', 'quiz_id' => 1],
            ['text' => 'What is the highest mountain in the world?', 'mark' => 10, 'answers' => json_encode(['K2', 'Kangchenjunga', 'Everest', 'Lhotse']), 'correct_answer' => 'Everest', 'quiz_id' => 1],
            ['text' => 'Who painted the Mona Lisa?', 'mark' => 10, 'answers' => json_encode(['Van Gogh', 'Picasso', 'Da Vinci', 'Michelangelo']), 'correct_answer' => 'Da Vinci', 'quiz_id' => 1],
            ['text' => 'What is the chemical symbol for gold?', 'mark' => 10, 'answers' => json_encode(['Au', 'Ag', 'Pb', 'Fe']), 'correct_answer' => 'Au', 'quiz_id' => 1],
            ['text' => 'What is the capital of Germany?', 'mark' => 10, 'answers' => json_encode(['Berlin', 'Munich', 'Frankfurt', 'Hamburg']), 'correct_answer' => 'Berlin', 'quiz_id' => 2],
            ['text' => 'What is the chemical formula for water?', 'mark' => 10, 'answers' => json_encode(['H2O', 'CO2', 'O2', 'H2']), 'correct_answer' => 'H2O', 'quiz_id' => 2],
            ['text' => 'What is the tallest building in the world?', 'mark' => 10, 'answers' => json_encode(['Burj Khalifa', 'Shanghai Tower', 'Abraj Al-Bait', 'One World Trade Center']), 'correct_answer' => 'Burj Khalifa', 'quiz_id' => 2],
            ['text' => 'Who developed the theory of relativity?', 'mark' => 10, 'answers' => json_encode(['Newton', 'Einstein', 'Galileo', 'Tesla']), 'correct_answer' => 'Einstein', 'quiz_id' => 2],
            ['text' => 'What is the largest ocean on Earth?', 'mark' => 10, 'answers' => json_encode(['Atlantic', 'Indian', 'Arctic', 'Pacific']), 'correct_answer' => 'Pacific', 'quiz_id' => 2],
            ['text' => 'What is the primary language spoken in Brazil?', 'mark' => 10, 'answers' => json_encode(['Spanish', 'Portuguese', 'English', 'French']), 'correct_answer' => 'Portuguese', 'quiz_id' => 2],
            ['text' => 'What is the powerhouse of the cell?', 'mark' => 10, 'answers' => json_encode(['Nucleus', 'Mitochondria', 'Ribosome', 'Chloroplast']), 'correct_answer' => 'Mitochondria', 'quiz_id' => 2],
            ['text' => 'What is the capital of Canada?', 'mark' => 10, 'answers' => json_encode(['Toronto', 'Ottawa', 'Vancouver', 'Montreal']), 'correct_answer' => 'Ottawa', 'quiz_id' => 2],
            ['text' => 'What is the largest mammal?', 'mark' => 10, 'answers' => json_encode(['Elephant', 'Blue Whale', 'Giraffe', 'Hippopotamus']), 'correct_answer' => 'Blue Whale', 'quiz_id' => 2],
            ['text' => 'What is the main ingredient in sushi?', 'mark' => 10, 'answers' => json_encode(['Fish', 'Rice', 'Seaweed', 'Soy Sauce']), 'correct_answer' => 'Rice', 'quiz_id' => 2],
            ['text' => 'What is the capital of France?', 'mark' => 10, 'answers' => json_encode(['Paris', 'London', 'Berlin', 'Madrid']), 'correct_answer' => 'Paris', 'quiz_id' => 5],
            ['text' => 'What is the capital of France?', 'mark' => 10, 'answers' => json_encode(['Paris', 'London', 'Berlin', 'Madrid']), 'correct_answer' => 'Paris', 'quiz_id' => 5],
            ['text' => 'What is the capital of France?', 'mark' => 10, 'answers' => json_encode(['Paris', 'London', 'Berlin', 'Madrid']), 'correct_answer' => 'Paris', 'quiz_id' => 6],
            ['text' => 'What is the capital of France?', 'mark' => 10, 'answers' => json_encode(['Paris', 'London', 'Berlin', 'Madrid']), 'correct_answer' => 'Paris', 'quiz_id' => 6],

        ];

        foreach ($questions as $question) {
            Question::create($question);
        }
    }
}
