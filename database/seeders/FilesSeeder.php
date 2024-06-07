<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Files;

class FilesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Files::create([
            'id' => '1',
            'name' => 'part 1',
            'unit_id' => '1',
            'subject_id' => '1',
            'lesson_id' => '1',
            'content' => '2.pdf',
            
        ]);
        Files::create([
            'id' => '2',
            'name' => 'part 1',
            'unit_id' => '1',
            'subject_id' => '1',
            'lesson_id' => '1',
            'content' => '2.pdf',
            
        ]);
    }
}
