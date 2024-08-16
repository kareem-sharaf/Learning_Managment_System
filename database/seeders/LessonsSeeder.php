<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lesson;

class LessonsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // English Lessons
        Lesson::create([
            'id' => 1,
            'name' => 'English Part 1',
            'unit_id' => 1, // Assuming 'Introduction to English' unit ID is 1
            'price' => 100,
            'description' => 'Basic English grammar and vocabulary.',
            'image' => 'english_part1.png',
            'teacher_id' => 3, // Assuming teacher with ID 3 is assigned
        ]);

        Lesson::create([
            'id' => 2,
            'name' => 'English Part 2',
            'unit_id' => 1, // Assuming 'Introduction to English' unit ID is 1
            'price' => 120,
            'description' => 'Advanced English vocabulary and comprehension.',
            'image' => 'english_part2.png',
            'teacher_id' => 3, // Assuming teacher with ID 3 is assigned
        ]);

        // C++ Lessons
        Lesson::create([
            'id' => 3,
            'name' => 'C++ Basics Part 1',
            'unit_id' => 2, // Assuming 'Basic C++' unit ID is 2
            'price' => 150,
            'description' => 'Introduction to C++ programming.',
            'image' => 'cpp_basics_part1.png',
            'teacher_id' => 4, // Assuming teacher with ID 4 is assigned
        ]);

        Lesson::create([
            'id' => 4,
            'name' => 'C++ Basics Part 2',
            'unit_id' => 2, // Assuming 'Basic C++' unit ID is 2
            'price' => 180,
            'description' => 'Intermediate concepts in C++ programming.',
            'image' => 'cpp_basics_part2.png',
            'teacher_id' => 4, // Assuming teacher with ID 4 is assigned
        ]);

        // Python Lessons
        Lesson::create([
            'id' => 5,
            'name' => 'Python Basics Part 1',
            'unit_id' => 3, // Assuming 'Python Basics' unit ID is 3
            'price' => 120,
            'description' => 'Learn the basics of Python programming.',
            'image' => 'python_basics_part1.png',
            'teacher_id' => 5, // Assuming teacher with ID 5 is assigned
        ]);

        Lesson::create([
            'id' => 6,
            'name' => 'Python Basics Part 2',
            'unit_id' => 3, // Assuming 'Python Basics' unit ID is 3
            'price' => 140,
            'description' => 'Intermediate concepts in Python programming.',
            'image' => 'python_basics_part2.png',
            'teacher_id' => 5, // Assuming teacher with ID 5 is assigned
        ]);

        // Algebra Lessons
        Lesson::create([
            'id' => 7,
            'name' => 'Algebra Part 1',
            'unit_id' => 4, // Assuming 'Algebra' unit ID is 4
            'price' => 110,
            'description' => 'Fundamentals of Algebra.',
            'image' => 'algebra_part1.png',
            'teacher_id' => 6, // Assuming teacher with ID 6 is assigned
        ]);

        Lesson::create([
            'id' => 8,
            'name' => 'Algebra Part 2',
            'unit_id' => 4, // Assuming 'Algebra' unit ID is 4
            'price' => 130,
            'description' => 'Advanced Algebra concepts.',
            'image' => 'algebra_part2.png',
            'teacher_id' => 6, // Assuming teacher with ID 6 is assigned
        ]);

        // Mechanics Lessons
        Lesson::create([
            'id' => 9,
            'name' => 'Mechanics Part 1',
            'unit_id' => 5, // Assuming 'Mechanics' unit ID is 5
            'price' => 130,
            'description' => 'Introduction to Physics mechanics.',
            'image' => 'mechanics_part1.png',
            'teacher_id' => 7, // Assuming teacher with ID 7 is assigned
        ]);

        Lesson::create([
            'id' => 10,
            'name' => 'Mechanics Part 2',
            'unit_id' => 5, // Assuming 'Mechanics' unit ID is 5
            'price' => 150,
            'description' => 'Advanced topics in Physics mechanics.',
            'image' => 'mechanics_part2.png',
            'teacher_id' => 7, // Assuming teacher with ID 7 is assigned
        ]);

        // Organic Chemistry Lessons
        Lesson::create([
            'id' => 11,
            'name' => 'Organic Chemistry Part 1',
            'unit_id' => 6, // Assuming 'Organic Chemistry' unit ID is 6
            'price' => 140,
            'description' => 'Basics of organic chemistry.',
            'image' => 'organic_chemistry_part1.png',
            'teacher_id' => 8, // Assuming teacher with ID 8 is assigned
        ]);

        Lesson::create([
            'id' => 12,
            'name' => 'Organic Chemistry Part 2',
            'unit_id' => 6, // Assuming 'Organic Chemistry' unit ID is 6
            'price' => 160,
            'description' => 'Advanced organic chemistry concepts.',
            'image' => 'organic_chemistry_part2.png',
            'teacher_id' => 8, // Assuming teacher with ID 8 is assigned
        ]);

        // Java Lessons
        Lesson::create([
            'id' => 13,
            'name' => 'Java Fundamentals Part 1',
            'unit_id' => 7, // Assuming 'Java Fundamentals' unit ID is 7
            'price' => 160,
            'description' => 'Introduction to Java programming.',
            'image' => 'java_fundamentals_part1.png',
            'teacher_id' => 9, // Assuming teacher with ID 9 is assigned
        ]);

        Lesson::create([
            'id' => 14,
            'name' => 'Java Fundamentals Part 2',
            'unit_id' => 7, // Assuming 'Java Fundamentals' unit ID is 7
            'price' => 180,
            'description' => 'Intermediate Java programming concepts.',
            'image' => 'java_fundamentals_part2.png',
            'teacher_id' => 9, // Assuming teacher with ID 9 is assigned
        ]);

        // Arabic Lessons
        Lesson::create([
            'id' => 15,
            'name' => 'Arabic Grammar Part 1',
            'unit_id' => 8, // Assuming 'Arabic Grammar' unit ID is 8
            'price' => 110,
            'description' => 'Introduction to Arabic grammar.',
            'image' => 'arabic_grammar_part1.png',
            'teacher_id' => 10, // Assuming teacher with ID 10 is assigned
        ]);

        Lesson::create([
            'id' => 16,
            'name' => 'Arabic Grammar Part 2',
            'unit_id' => 8, // Assuming 'Arabic Grammar' unit ID is 8
            'price' => 130,
            'description' => 'Advanced Arabic grammar topics.',
            'image' => 'arabic_grammar_part2.png',
            'teacher_id' => 10, // Assuming teacher with ID 10 is assigned
        ]);
    }
}
