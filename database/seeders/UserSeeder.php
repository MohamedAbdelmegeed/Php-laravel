<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Every account below uses the well-known password "password", and
        // User::canAccessPanel() admits any authenticated user to /admin - so
        // running this on the server would hand the panel to anyone who reads
        // this file. `db:seed --force` bypasses Laravel's own prompt, hence
        // the explicit check rather than relying on it.
        if (app()->isProduction()) {
            $this->command?->warn('UserSeeder skipped: it creates accounts with a publicly known password.');

            return;
        }

        // A fixed login, so there is always a known way into /admin without
        // digging a generated address out of the database. firstOrCreate keeps
        // repeat runs from tripping the unique index on email.
        User::firstOrCreate(
            ['email' => 'dev@example.com'],
            [
                'name' => 'Dev Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        // Filler rows for exercising pagination, search and sorting in tables.
        // The factory generates unique emails, so each run appends another 25
        // instead of replacing the previous batch.
        User::factory()->count(25)->create();
    }
}
