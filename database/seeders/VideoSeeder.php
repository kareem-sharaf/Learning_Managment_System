<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Video;

class VideoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Video::create([
            'id' => '1',
            'name' => 'part 1',
            'video'=>'1',
            'unit_id' => '1',
            'subject_id' => '0',
            'lesson_id' => '0',
            'ad_id' => '1',

        ]);
        Video::create([
            'id' => '2',
            'name' => 'part 1',
            'video'=>'1',
            'unit_id' => '2',
            'subject_id' => '0',
            'lesson_id' => '0',
            'ad_id' => '1',

        ]);
    }
}
