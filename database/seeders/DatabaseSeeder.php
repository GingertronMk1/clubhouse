<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([SportSeeder::class, PositionSeeder::class, UserSeeder::class, ClubSeeder::class, TeamSeeder::class, CompetitionSeeder::class, GameSeeder::class]);
    }
}
