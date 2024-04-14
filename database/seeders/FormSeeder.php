<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Form;

class FormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Form::create([
            'id' => '1',
            'FormName' => 'Study Zone'
        ]);
        Form::create([
            'id' => '2',
            'FormName' => 'Learn Zone'
        ]);
    }
}
