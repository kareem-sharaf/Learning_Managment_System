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
            'name'
            => 'part 1',
            'video' => '1',
            'type_id' => 5,
            'type_type' => 'App\Models\Subject',


        ]);
        Video::create([
            'id' => '2',
            'name' => 'part 1',
            'video' => '1',
            'type_id' => 4,
            'type_type' => 'App\Models\Subject',


        ]);
    }
}
