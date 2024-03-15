<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Stage;

class StageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stages = [
            ['id' => 1, 'Stage' => 'elementary'],
            ['id' => 2, 'Stage' => 'middle school'],
            ['id' => 3, 'Stage' => 'high school (scientific)'],
            ['id' => 4, 'Stage' => 'high school (literary)'],
        ];

        foreach ($stages as $stageData) {
            // Check if the stage exists
            $existingStage = Stage::where('id', $stageData['id'])->first();

            // If the stage doesn't exist, create it
            if (!$existingStage) {
                Stage::create($stageData);
            }
        }
    }
}
