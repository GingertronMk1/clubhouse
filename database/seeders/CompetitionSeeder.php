<?php

namespace Database\Seeders;

use App\Models\Competition;
use App\Models\Location;
use Illuminate\Database\Seeder;

class CompetitionSeeder extends Seeder
{
    public function run(): void
    {
        $competitions = [
            ['2026 NFL season', 'The 2026 National Football League regular season.', 'NFL venues'],
            ['2026 NRL season', 'The 2026 National Rugby League season, including finals.', 'NRL venues'],
            ['2026 Super League season', 'The 2026 Super League season, including play-offs.', 'Super League venues'],
            ['2024 Touch World Cup', 'The 2024 Touch World Cup in Nottingham, England.', 'Highfields Sports Ground, Nottingham'],
        ];

        foreach ($competitions as [$name, $description, $locationName]) {
            $location = Location::query()->firstOrCreate(
                ['name' => $locationName],
                ['description' => $locationName, 'additional_information' => []],
            );

            Competition::query()->without(['parent', 'location'])->firstOrCreate(
                ['name' => $name],
                ['description' => $description, 'location_id' => $location->id],
            );
        }
    }
}
