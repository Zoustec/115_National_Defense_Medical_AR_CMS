<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL') ?: throw new RuntimeException('ADMIN_EMAIL must be set in .env before seeding.');
        $password = env('ADMIN_PASSWORD') ?: throw new RuntimeException('ADMIN_PASSWORD must be set in .env before seeding.');

        Admin::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Admin User',
                'password' => Hash::make($password),
                'is_active' => true,
            ]
        );
    }
}
