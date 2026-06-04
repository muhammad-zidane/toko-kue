<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'title'    => 'Kue Ulang Tahun Spesial',
                'subtitle' => 'Pesan sekarang, kirim hari ini',
                'image'    => null,
                'link'     => '/products?category=kue-ulang-tahun',
                'is_active' => true,
                'order'    => 1,
            ],
            [
                'title'    => 'Promo Spesial Hari Ini',
                'subtitle' => 'Diskon hingga 20% untuk semua kue kering',
                'image'    => null,
                'link'     => '/products?category=kue-kering',
                'is_active' => true,
                'order'    => 2,
            ],
            [
                'title'    => 'Custom Kue Pernikahan',
                'subtitle' => 'Buat hari istimewa semakin berkesan',
                'image'    => null,
                'link'     => '/products?category=kue-pernikahan',
                'is_active' => true,
                'order'    => 3,
            ],
        ];

        Banner::upsert($banners, ['title'], ['subtitle', 'image', 'link', 'is_active', 'order']);
    }
}
