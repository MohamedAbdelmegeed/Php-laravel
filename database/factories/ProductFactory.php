<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => ucfirst(fake()->words(3, true)),
            'sku' => strtoupper(fake()->unique()->bothify('??-####')),
            'description' => fake()->paragraph(),
            'price_cents' => fake()->numberBetween(500, 250000),
            'stock' => fake()->numberBetween(0, 500),
            'category' => fake()->randomElement(array_keys(Product::CATEGORIES)),
            'is_active' => fake()->boolean(85),
        ];
    }

    public function outOfStock(): static
    {
        return $this->state(fn (): array => ['stock' => 0]);
    }
}
