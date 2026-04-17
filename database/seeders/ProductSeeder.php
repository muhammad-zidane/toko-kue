<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Kue Ulang Tahun Coklat',  'category_id' => 1, 'price' => 250000, 'stock' => 10],
            ['name' => 'Kue Ulang Tahun Vanilla',  'category_id' => 1, 'price' => 220000, 'stock' => 10],
            ['name' => 'Kue Pernikahan 3 Tingkat',  'category_id' => 2, 'price' => 850000, 'stock' => 5],
            ['name' => 'Kue Pernikahan Elegant',    'category_id' => 2, 'price' => 750000, 'stock' => 5],
            ['name' => 'Nastar',                    'category_id' => 3, 'price' => 85000,  'stock' => 50],
            ['name' => 'Putri Salju',               'category_id' => 3, 'price' => 80000,  'stock' => 50],
            ['name' => 'Brownies Panggang',         'category_id' => 4, 'price' => 120000, 'stock' => 20],
            ['name' => 'Brownies Kukus',            'category_id' => 4, 'price' => 110000, 'stock' => 20],
            ['name' => 'Cupcake Rainbow',           'category_id' => 5, 'price' => 150000, 'stock' => 15],
            ['name' => 'Cupcake Red Velvet',        'category_id' => 5, 'price' => 160000, 'stock' => 15],
        ];

        foreach ($products as $product) {
            Product::create([
                'name'         => $product['name'],
                'slug'         => Str::slug($product['name']),
                'category_id'  => $product['category_id'],
                'price'        => $product['price'],
                'stock'        => $product['stock'],
                'description'  => 'Deskripsi ' . $product['name'],
                'is_available' => true,
            ]);
        }
    }
}