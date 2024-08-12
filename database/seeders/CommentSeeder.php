<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Comment;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Comment::create([
            'id' => '1',
            'user_id' => '1',
            'lesson_id'=>'1',
            'content' => 'hi',
            'reply_to'=>'2',
        ]);
        Comment::create([
            'id' => '2',
            'user_id' => '2',
            'lesson_id'=>'2',
            'content' => 'hi',
            'reply_to'=>'1',

        ]);
    }
}
