<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Classification;

class ClassificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Classification::create([
            'id' => 1,
            'class' => 'educational',
            'form_id' => 2
        ]);
        Classification::create([
            'id' => 2,
            'class' => 'languages',
            'form_id' => 2
        ]);
        Classification::create([
            'id' => 3,
            'class' => 'programming',
            'form_id' => 2
        ]);
    }
}
