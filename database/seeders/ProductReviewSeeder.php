<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\ProductReview;
use Illuminate\Database\Seeder;

class ProductReviewSeeder extends Seeder
{
    public function run(): void
    {
        $komentar = [
            'Rasanya enak banget, teksturnya lembut dan pas!',
            'Kuenya sesuai ekspektasi, recommended!',
            'Pengiriman cepat, kue masih segar sampai tujuan.',
            'Sudah beli beberapa kali, selalu memuaskan.',
            'Tampilannya cantik, rasanya pun tidak mengecewakan.',
            'Harga sesuai kualitas, layak dibeli lagi.',
            'Kue favorit keluarga kami sekarang!',
            'Kualitasnya konsisten, saya suka!',
        ];

        $index = 0;
        Order::with('orderItems')
            ->whereIn('status', ['completed', 'selesai'])
            ->chunk(50, function ($orders) use (&$index, $komentar) {
                foreach ($orders as $order) {
                    foreach ($order->orderItems as $item) {
                        ProductReview::firstOrCreate(
                            [
                                'user_id'    => $order->user_id,
                                'product_id' => $item->product_id,
                                'order_id'   => $order->id,
                            ],
                            [
                                'rating'      => rand(3, 5),
                                'comment'     => $komentar[($index + $item->product_id) % count($komentar)],
                                'is_approved' => $index % 3 !== 0,
                            ]
                        );
                    }
                    $index++;
                }
            });
    }
}
