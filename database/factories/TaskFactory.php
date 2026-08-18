<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    public function definition(): array
    {
        $status = fake()->randomElement(array_keys(Task::STATUSES));

        return [
            'user_id' => User::query()->inRandomOrder()->value('id'),
            'title' => ucfirst(fake()->words(5, true)),
            'description' => fake()->optional()->paragraph(),
            'priority' => fake()->randomElement(array_keys(Task::PRIORITIES)),
            'status' => $status,
            'due_date' => fake()->optional(0.8)->dateTimeBetween('-2 months', '+2 months'),
            // Keeps the data self-consistent: only done tasks carry a
            // completion timestamp, so the overdue scope means what it says.
            'completed_at' => $status === 'done' ? fake()->dateTimeBetween('-1 month') : null,
        ];
    }

    public function overdue(): static
    {
        return $this->state(fn (): array => [
            'due_date' => fake()->dateTimeBetween('-2 months', '-1 day'),
            'status' => fake()->randomElement(['todo', 'in_progress']),
            'completed_at' => null,
        ]);
    }
}
