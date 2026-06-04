<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id'       => Order::factory(),
            'payment_method' => 'transfer_bank',
            'status'         => 'unpaid',
            'amount'         => fake()->numberBetween(50000, 500000),
            'proof_image'    => null,
            'paid_at'        => null,
        ];
    }

    public function paid(): static
    {
        return $this->state([
            'status'  => 'paid',
            'paid_at' => now(),
        ]);
    }

    public function failed(): static
    {
        return $this->state([
            'status' => 'failed',
        ]);
    }
}
