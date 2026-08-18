<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Product;
use App\Models\Task;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        // Same reasoning as UserSeeder: this is browsing material for local
        // work, not something that belongs in a real database.
        if (app()->isProduction()) {
            $this->command?->warn('ContentSeeder skipped: dummy data is for local use only.');

            return;
        }

        // PostFactory picks an existing author, so users must already exist.
        // Called explicitly rather than assumed, so this seeder stands alone.
        if (\App\Models\User::query()->doesntExist()) {
            $this->call(UserSeeder::class);
        }

        Post::factory()->count(30)->create();
        Post::factory()->draft()->count(8)->create();

        Product::factory()->count(40)->create();
        Product::factory()->outOfStock()->count(6)->create();

        Task::factory()->count(35)->create();
        Task::factory()->overdue()->count(9)->create();
    }
}
