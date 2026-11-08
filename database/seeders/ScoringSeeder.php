<?php

namespace Database\Seeders;

use App\Models\Scoring;
use Illuminate\Database\Seeder;

class ScoringSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Scoring::factory()->create();
    }
}
