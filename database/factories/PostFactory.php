<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->sentence(6);
        $published = fake()->boolean(70);

        return [
            // Reuses an existing user when there is one, so seeding posts does
            // not silently create a parallel set of users nobody asked for.
            'user_id' => User::query()->inRandomOrder()->value('id') ?? User::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1, 999999),
            'excerpt' => fake()->paragraph(),
            'body' => fake()->paragraphs(6, true),
            'cover_image' => null,
            'is_published' => $published,
            'published_at' => $published ? fake()->dateTimeBetween('-1 year') : null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (): array => [
            'is_published' => true,
            'published_at' => fake()->dateTimeBetween('-6 months'),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (): array => [
            'is_published' => false,
            'published_at' => null,
        ]);
    }
}
