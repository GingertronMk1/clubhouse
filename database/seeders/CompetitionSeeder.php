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

        foreach ([
            ['2025–26 United Rugby Championship season', 'The 2025–26 United Rugby Championship season, including play-offs.', 'United Rugby Championship venues', '1283f3b2-ee8f-4ad7-929f-f0bc3631ece5', '25dc85c9-2728-40c2-8388-bba2d1c638a7'],
            ['2025–26 Premiership Rugby season', 'The 2025–26 Premiership Rugby season, including play-offs.', 'Premiership Rugby venues', '4d69e5e0-70b6-4b94-999e-79b16699ba3d', '06770b14-2235-4cce-b0d5-7a701f2d4fd6'],
        ] as [$name, $description, $locationName, $competitionId, $locationId]) {
            $location = Location::query()->firstOrNew(['name' => $locationName], ['description' => $locationName, 'additional_information' => []]);
            if (! $location->exists) {
                $location->id = $locationId;
                $location->save();
            }

            $competition = Competition::query()->without(['parent', 'location'])->firstOrNew(
                ['name' => $name],
                ['description' => $description, 'location_id' => $location->id],
            );
            if (! $competition->exists) {
                $competition->id = $competitionId;
                $competition->save();
            }
        }
    }
}
