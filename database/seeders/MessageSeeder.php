<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MessageModel;
class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MessageModel::create([
            'id' => '1',
            'user_id' => ' 1',
            'message' => 'who are you ',
        ]);
        MessageModel::create([
            'id' => '2',
            'user_id' => ' 1',
            'message' => 'I have problem',
        ]);
    }
}
