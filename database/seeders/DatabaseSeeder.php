<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            ShippingZoneSeeder::class,
            TestimonialSeeder::class,
            BannerSeeder::class,
            CustomizationOptionSeeder::class,
            VoucherSeeder::class,
            DemoSeeder::class,
            ProductReviewSeeder::class,
        ]);
    }
}
