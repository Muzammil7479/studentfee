<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // The demo "Test User" relies on fakerphp/faker, which is a
        // require-dev package. Production deploys typically run
        // `composer install --no-dev`, which would make this call fatal-error
        // and abort the whole seed run (including the real admin/user
        // accounts below). Guarding it keeps local `php artisan db:seed`
        // behavior identical while making production seeding safe.
        if (class_exists(\Faker\Factory::class)) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }

        $this->call([
            AdminUserSeeder::class,
            TermSeeder::class,
        ]);
    }
}
