<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create([
            'id' => 1,
            'category' => 'educational'
        ]);
        Category::create([
            'id' => 2,
            'category' => 'languages'
        ]);
        Category::create([
            'id' => 3,
            'category' => 'programming'
        ]);
    }
}
