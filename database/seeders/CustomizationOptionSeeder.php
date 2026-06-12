<?php

namespace Database\Seeders;

use App\Models\CustomizationOption;
use Illuminate\Database\Seeder;

class CustomizationOptionSeeder extends Seeder
{
    public function run(): void
    {
        // category_id: 1=Kue Ulang Tahun, 2=Kue Pernikahan, null=berlaku semua kategori
        $options = [
            // Rasa — berlaku semua kategori
            ['type' => 'rasa', 'name' => 'Coklat',      'extra_price' => 0,     'category_id' => null, 'sort_order' => 1],
            ['type' => 'rasa', 'name' => 'Vanilla',     'extra_price' => 0,     'category_id' => null, 'sort_order' => 2],
            ['type' => 'rasa', 'name' => 'Pandan',      'extra_price' => 0,     'category_id' => null, 'sort_order' => 3],
            ['type' => 'rasa', 'name' => 'Red Velvet',  'extra_price' => 15000, 'category_id' => null, 'sort_order' => 4],
            ['type' => 'rasa', 'name' => 'Taro',        'extra_price' => 15000, 'category_id' => null, 'sort_order' => 5],

            // Ukuran — Kue Ulang Tahun (category_id=1)
            ['type' => 'ukuran', 'name' => 'Bulat 18cm', 'extra_price' => 0,      'category_id' => 1, 'sort_order' => 1],
            ['type' => 'ukuran', 'name' => 'Bulat 22cm', 'extra_price' => 50000,  'category_id' => 1, 'sort_order' => 2],
            ['type' => 'ukuran', 'name' => 'Bulat 26cm', 'extra_price' => 100000, 'category_id' => 1, 'sort_order' => 3],

            // Ukuran — Kue Pernikahan (category_id=2)
            ['type' => 'ukuran', 'name' => '1 Tingkat', 'extra_price' => 0,      'category_id' => 2, 'sort_order' => 1],
            ['type' => 'ukuran', 'name' => '2 Tingkat', 'extra_price' => 300000, 'category_id' => 2, 'sort_order' => 2],
            ['type' => 'ukuran', 'name' => '3 Tingkat', 'extra_price' => 600000, 'category_id' => 2, 'sort_order' => 3],

            // Topping — berlaku semua kategori
            ['type' => 'topping', 'name' => 'Sprinkles',    'extra_price' => 10000, 'category_id' => null, 'sort_order' => 1],
            ['type' => 'topping', 'name' => 'Fresh Fruit',  'extra_price' => 25000, 'category_id' => null, 'sort_order' => 2],
            ['type' => 'topping', 'name' => 'Coklat Kerik', 'extra_price' => 15000, 'category_id' => null, 'sort_order' => 3],
            ['type' => 'topping', 'name' => 'Krim Keju',    'extra_price' => 20000, 'category_id' => null, 'sort_order' => 4],

            // Lainnya — berlaku semua kategori
            ['type' => 'lainnya', 'name' => 'Tanpa Gluten',   'extra_price' => 20000, 'category_id' => null, 'sort_order' => 1],
            ['type' => 'lainnya', 'name' => 'Tulisan Custom', 'extra_price' => 10000, 'category_id' => null, 'sort_order' => 2],
        ];

        $rows = array_map(fn ($o) => $o + ['is_active' => true], $options);

        CustomizationOption::upsert(
            $rows,
            ['name', 'type'],
            ['extra_price', 'category_id', 'sort_order', 'is_active']
        );
    }
}
