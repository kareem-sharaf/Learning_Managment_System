<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Address;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Address::create([
            'id' => '1',
            'address' => 'damascus'
        ]);
        Address::create([
            'id' => '2',
            'address' => 'aleppo'
        ]);
        Address::create([
            'id' => '3',
            'address' => 'homs'
        ]);
        Address::create([
            'id' => '4',
            'address' => 'latakia'
        ]);
        Address::create([
            'id' => '5',
            'address' => 'tartous'
        ]);
        Address::create([
            'id' => '6',
            'address' => 'hama'
        ]);
        Address::create([
            'id' => '7',
            'address' => 'raqa'
        ]);
        Address::create([
            'id' => '8',
            'address' => 'hasakeh'
        ]);
        Address::create([
            'id' => '9',
            'address' => 'daraa'
        ]);
        Address::create([
            'id' => '10',
            'address' => 'swieda'
        ]);
        Address::create([
            'id' => '11',
            'address' => 'edleb'
        ]);
        Address::create([
            'id' => '12',
            'address' => 'dier azour'
        ]);
        Address::create([
            'id' => '13',
            'address' => 'jawlan'
        ]);
        Address::create([
            'id' => '14',
            'address' => 'qamashli'
        ]);
        // Address::create([
        //     'id' => '15',
        //     'address' => ''
        // ]);
        // Address::create([
        //     'id' => '16',
        //     'address' => ''
        // ]);
        // Address::create([
        //     'id' => '17',
        //     'address' => ''
        // ]);
        // Address::create([
        //     'id' => '18',
        //     'address' => ''
        // ]);
    }
}
