<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(['email' => 'admin@example.com'], [
            'name' => 'Admin Platform',
            'password' => 'password',
            'role' => User::ROLE_ADMIN,
            'email_verified_at' => now(),
        ]);

        $user = User::query()->updateOrCreate(['email' => 'user@example.com'], [
            'name' => 'Budi Santoso',
            'password' => 'password',
            'role' => User::ROLE_USER,
            'phone' => '081234567890',
            'email_verified_at' => now(),
        ]);

        $user->subscriptions()->firstOrCreate(['plan' => 'pro'], [
            'status' => 'active',
            'price' => config('platform.plans.pro.price'),
            'starts_at' => now()->subMonths(2),
        ]);
    }
}
