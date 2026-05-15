<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'category_id'  => Category::factory(),
            'name'         => $name,
            'slug'         => Str::slug($name) . '-' . fake()->unique()->randomNumber(4),
            'description'  => fake()->paragraph(),
            'price'        => fake()->numberBetween(25000, 500000),
            'stock'        => fake()->numberBetween(5, 100),
            'image'        => null,
            'is_available' => true,
            'badge'        => null,
        ];
    }

    public function unavailable(): static
    {
        return $this->state(['is_available' => false, 'stock' => 0]);
    }

    public function outOfStock(): static
    {
        return $this->state(['stock' => 0]);
    }
}
