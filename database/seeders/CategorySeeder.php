<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Kue Ulang Tahun', 'description' => 'Kue spesial untuk perayaan ulang tahun'],
            ['name' => 'Kue Pernikahan',  'description' => 'Kue elegan untuk hari pernikahan'],
            ['name' => 'Kue Kering',      'description' => 'Aneka kue kering untuk berbagai acara'],
            ['name' => 'Brownies',        'description' => 'Brownies lezat dengan berbagai topping'],
            ['name' => 'Cupcake',         'description' => 'Cupcake cantik dan lezat'],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name'        => $category['name'],
                'slug'        => Str::slug($category['name']),
                'description' => $category['description'],
            ]);
        }
    }
}