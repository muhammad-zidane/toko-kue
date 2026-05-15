<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VoucherFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code'         => strtoupper(Str::random(8)),
            'type'         => fake()->randomElement(['fixed', 'percent']),
            'value'        => fake()->numberBetween(5000, 50000),
            'usage_limit'  => 10,
            'used_count'   => 0,
            'min_purchase' => 50000,
            'is_active'    => true,
            'expires_at'   => now()->addDays(30),
        ];
    }
}
