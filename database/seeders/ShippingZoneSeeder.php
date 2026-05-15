<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShippingZoneSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('shipping_zones')->truncate();

        $zones = [
            // Kota
            ['area_name' => 'Kota Payakumbuh',              'cost' => 10000, 'is_available' => true],
            ['area_name' => 'Kota Bukittinggi',              'cost' => 20000, 'is_available' => true],
            ['area_name' => 'Kota Padang',                   'cost' => 25000, 'is_available' => true],
            ['area_name' => 'Kota Padang Panjang',           'cost' => 20000, 'is_available' => true],
            ['area_name' => 'Kota Solok',                    'cost' => 25000, 'is_available' => true],
            ['area_name' => 'Kota Sawahlunto',               'cost' => 30000, 'is_available' => true],
            ['area_name' => 'Kota Pariaman',                 'cost' => 30000, 'is_available' => true],
            // Kabupaten
            ['area_name' => 'Kabupaten Lima Puluh Kota',     'cost' => 15000, 'is_available' => true],
            ['area_name' => 'Kabupaten Agam',                'cost' => 20000, 'is_available' => true],
            ['area_name' => 'Kabupaten Tanah Datar',         'cost' => 20000, 'is_available' => true],
            ['area_name' => 'Kabupaten Padang Pariaman',     'cost' => 25000, 'is_available' => true],
            ['area_name' => 'Kabupaten Pasaman',             'cost' => 25000, 'is_available' => true],
            ['area_name' => 'Kabupaten Pasaman Barat',       'cost' => 30000, 'is_available' => true],
            ['area_name' => 'Kabupaten Solok',               'cost' => 25000, 'is_available' => true],
            ['area_name' => 'Kabupaten Solok Selatan',       'cost' => 35000, 'is_available' => true],
            ['area_name' => 'Kabupaten Sijunjung',           'cost' => 30000, 'is_available' => true],
            ['area_name' => 'Kabupaten Dharmasraya',         'cost' => 35000, 'is_available' => true],
            ['area_name' => 'Kabupaten Pesisir Selatan',     'cost' => 30000, 'is_available' => true],
            ['area_name' => 'Kabupaten Kepulauan Mentawai',  'cost' => 50000, 'is_available' => true],
        ];

        foreach ($zones as $zone) {
            DB::table('shipping_zones')->insert(array_merge($zone, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
