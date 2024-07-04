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
            'sender_id' => ' 2',
            'receiver_id'=>'3',
            'message' => 'who are you '
        ]);
        MessageModel::create([
            'id' => '2',
            'sender_id' => ' 2',
            'receiver_id'=>'1',
            'message' => 'iam you '
        ]);
    }
}
