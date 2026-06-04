<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'          => User::factory(),
            'order_code'       => 'ORD-' . strtoupper(Str::random(8)),
            'status'           => 'pending',
            'delivery_method'  => 'pickup',
            'shipping_address' => 'Ambil di Toko',
            'shipping_cost'    => 0,
            'total_price'      => fake()->numberBetween(50000, 500000),
            'notes'            => null,
            'delivery_date'    => now()->addDays(3)->format('Y-m-d'),
            'delivery_slot'    => null,
            'voucher_code'     => null,
            'discount_amount'  => 0,
            'payment_status'   => 'unpaid',
            'dp_amount'        => 0,
            'paid_amount'      => 0,
        ];
    }

    public function delivery(): static
    {
        return $this->state([
            'delivery_method'  => 'delivery',
            'shipping_address' => fake()->address(),
            'shipping_cost'    => 15000,
        ]);
    }
}
