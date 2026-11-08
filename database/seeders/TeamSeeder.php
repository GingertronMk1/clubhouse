<?php

namespace Database\Seeders;

use App\Models\Club;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        $teamNames = ['First team', 'Second team', 'Third team', 'Social team'];

        foreach (Club::cursor() as $club) {
            foreach ($teamNames as $name) {
                $club->teams()->withTrashed()->firstOrCreate(
                    ['name' => $name],
                    ['description' => "{$name} for {$club->name}."],
                );
            }
        }
    }
}
