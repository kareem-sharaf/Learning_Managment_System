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
            'category' => 'educational',
            'image' =>'http://127.0.0.1:8000/category_images/1725098376.jpg'
        ]);
        Category::create([
            'id' => 2,
            'category' => 'languages',
            'image' =>'http://127.0.0.1:8000/category_images/1725098376.jpg'
        ]);
        Category::create([
            'id' => 3,
            'category' => 'programming',
            'image' =>'http://127.0.0.1:8000/category_images/1725098376.jpg'
        ]);
    }
}
