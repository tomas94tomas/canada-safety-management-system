<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('locations')->insert([
            [
                'value' => 'Vilnius',
                'sensus_id' => 33,
            ],
            [
                'value' => 'Kaunas',
                'sensus_id' => 31
            ],
            [
                'value' => 'Line',
                'sensus_id' => 32
            ]
        ]);
    }
}
