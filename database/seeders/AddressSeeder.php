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
            'Address' => 'damascus'
        ]);
        Address::create([
            'id' => '2',
            'Address' => 'aleppo'
        ]);
        Address::create([
            'id' => '3',
            'Address' => 'homs'
        ]);
        Address::create([
            'id' => '4',
            'Address' => 'latakia'
        ]);
        Address::create([
            'id' => '5',
            'Address' => 'tartous'
        ]);
        Address::create([
            'id' => '6',
            'Address' => 'hama'
        ]);
        Address::create([
            'id' => '7',
            'Address' => 'raqa'
        ]);
        Address::create([
            'id' => '8',
            'Address' => 'hasakeh'
        ]);
        Address::create([
            'id' => '9',
            'Address' => 'daraa'
        ]);
        Address::create([
            'id' => '10',
            'Address' => 'swieda'
        ]);
        Address::create([
            'id' => '11',
            'Address' => 'edleb'
        ]);
        Address::create([
            'id' => '12',
            'Address' => 'dier azour'
        ]);
        Address::create([
            'id' => '13',
            'Address' => 'jawlan'
        ]);
        Address::create([
            'id' => '14',
            'Address' => 'qamashli'
        ]);
        Address::create([
            'id' => '15',
            'Address' => ''
        ]);
        Address::create([
            'id' => '16',
            'Address' => ''
        ]);
        Address::create([
            'id' => '17',
            'Address' => ''
        ]);
        Address::create([
            'id' => '18',
            'Address' => ''
        ]);
    }
}
