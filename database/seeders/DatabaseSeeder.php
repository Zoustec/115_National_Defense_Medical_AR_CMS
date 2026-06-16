<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            CategorySeeder::class,
            ItemSeeder::class,
            RecommendItemSeeder::class,
            LearningUnitSeeder::class,
        ]);
    }
}
