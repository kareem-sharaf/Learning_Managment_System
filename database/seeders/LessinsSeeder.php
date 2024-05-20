<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Lesson;

class LessinsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Lesson::create([
            'id' => '1',
            'name' => 'part 1',
            'unit_id' => '2',
            'price' => '2',
            'description' => '2',
            'image' => '2',
            'file' => '2',
            'video' => '2',
        ]);
        Lesson::create([
            'id' => '2',
            'name' => 'prprprprprprp',
            'unit_id' => '1',
            'price' => '2',
            'description' => '2',
            'image' => '2',
            'file' => '2',
            'video' => '2',
        ]);
    }
}
