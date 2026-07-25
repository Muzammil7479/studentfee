<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the default Admin and User accounts.
     *
     * Uses updateOrCreate so this seeder is safe to re-run — it will not
     * create duplicate rows if the accounts already exist.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@school.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin123'),
                'role' => User::ROLE_ADMIN,
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@school.com'],
            [
                'name' => 'Staff User',
                'password' => Hash::make('user123'),
                'role' => User::ROLE_USER,
            ]
        );
    }
}
