<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Year;

class YearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Year::create([
            'id' => '1',
            'year' => '1st',
            'stage_id' => '1'
        ]);
        Year::create([
            'id' => '2',
            'year' => '2nd',
            'stage_id' => '1'
        ]);
        Year::create([
            'id' => '3',
            'year' => '3rd',
            'stage_id' => '1'
        ]);
        Year::create([
            'id' => '4',
            'year' => '4th',
            'stage_id' => '1'
        ]);
        Year::create([
            'id' => '5',
            'year' => '5th',
            'stage_id' => '1'
        ]);
        Year::create([
            'id' => '6',
            'year' => '6th',
            'stage_id' => '1'
        ]);
        Year::create([
            'id' => '7',
            'year' => '1st',
            'stage_id' => '2'
        ]);
        Year::create([
            'id' => '8',
            'year' => '2nd',
            'stage_id' => '2'
        ]);
        Year::create([
            'id' => '9',
            'year' => '3rd',
            'stage_id' => '2'
        ]);
        Year::create([
            'id' => '10',
            'year' => '1st',
            'stage_id' => '3'
        ]);
        Year::create([
            'id' => '11',
            'year' => '2nd',
            'stage_id' => '3'
        ]);
        Year::create([
            'id' => '12',
            'year' => '3rd',
            'stage_id' => '3'
        ]);
        Year::create([
            'id' => '13',
            'year' => '1st',
            'stage_id' => '4'
        ]);
        Year::create([
            'id' => '14',
            'year' => '2nd',
            'stage_id' => '4'
        ]);
        Year::create([
            'id' => '15',
            'year' => '3rd',
            'stage_id' => '4'
        ]);
    }
}
