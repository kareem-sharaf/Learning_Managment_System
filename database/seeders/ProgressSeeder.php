<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Progress;

class ProgressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Progress::create([
            'user_id' => 9,
            'subject_id' => 1,
            'completed_videos' => json_encode([1, 2, 3]),
        ]);
        Progress::create([
            'user_id' => 10,
            'subject_id' => 1,
            'completed_videos' => json_encode([1]),
         ]);
         Progress::create([
            'user_id' => 10,
            'subject_id' => 2,
            'completed_videos' => json_encode([1,2]),
         ]);

    }
}
