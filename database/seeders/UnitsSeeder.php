<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Unit::create([
            'id' => '1',
            'name' => 'people',
            'description' => 'asdf',
            'price' => '2',
            'image_data' => '2',
            'video_id' => '2',
            'file_id' => '2',
            'subject_id' => '2',
        ]);
        Unit::create([
            'id' => '2',
            'name' => 'pharmacy',
            'description' => 'asdf',
            'price' => '2',
            'image_data' => '2',
            'video_id' => '2',
            'file_id' => '2',
            'subject_id' => '2',
        ]);
    }
}
