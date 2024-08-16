<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ADSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Sample data for ads
        $ads = [
            [
                'title' => 'Ad Title 1',
                'description' => 'Description for ad 1.',
                'image_url' => '/storage/ad_images/lJosb5rs6TNA0DGksXqpwQ3JD42ho22lpdzdiSet.png',
                'isExpired' => 0,
                'category_id' => 1,
                'video_id' => null,
            ],
            [
                'title' => 'Ad Title 2',
                'description' => 'Description for ad 2.',
                'image_url' => '/storage/ad_images/N34a30Am2bKpaT0i0VHG3Q574pTuWmMf3UnUwCVh.png',
                'isExpired' => 0,
                'category_id' => 2,
                'video_id' => null,
            ],
        ];

        DB::table('a_d_s')->insert($ads);
    }
}
