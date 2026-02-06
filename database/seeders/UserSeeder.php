<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => UserRole::ADMIN,
        ]);

        User::create([
            'name' => 'User Test',
            'email' => 'user@example.com',
            'password' => 'password',
            'role' => UserRole::USER,
        ]);

        User::create([
            'name' => 'External API Client',
            'email' => 'external@api.com',
            'password' => 'external123',
            'role' => UserRole::USER,
            'is_external' => true,
        ]);
    }
}
