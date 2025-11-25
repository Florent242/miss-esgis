<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            MissSeeder::class,
            AdminUserSeeder::class,
            SuperModSeeder::class,
            // Add other seeders here if you have them, e.g., UserSeeder for admins
        ]);
    }
}
