<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClubSeeder extends Seeder
{
    public function run(): void
    {
        $competitions = [
            'Rugby league club in the 2026 Super League.' => [
                'Bradford Bulls', 'Castleford Tigers', 'Catalans Dragons', 'Huddersfield Giants',
                'Hull FC', 'Hull KR', 'Leeds Rhinos', 'Leigh Leopards', 'St Helens',
                'Toulouse Olympique', 'Warrington Wolves', 'Wakefield Trinity', 'Wigan Warriors', 'York Knights',
            ],
            'Rugby league club in the 2026 NRL.' => [
                'Brisbane Broncos', 'Canberra Raiders', 'Canterbury-Bankstown Bulldogs',
                'Cronulla-Sutherland Sharks', 'Dolphins', 'Gold Coast Titans', 'Manly Warringah Sea Eagles',
                'Melbourne Storm', 'Newcastle Knights', 'New Zealand Warriors', 'North Queensland Cowboys',
                'Parramatta Eels', 'Penrith Panthers', 'South Sydney Rabbitohs',
                'St George Illawarra Dragons', 'Sydney Roosters', 'Wests Tigers',
            ],
            'Rugby union club in the 2026–27 English PREM.' => [
                'Bath Rugby', 'Bristol Bears', 'Exeter Chiefs', 'Gloucester Rugby', 'Harlequins',
                'Leicester Tigers', 'Newcastle Red Bulls', 'Northampton Saints', 'Sale Sharks', 'Saracens',
            ],
            'Rugby union club in the 2026–27 United Rugby Championship.' => [
                'Benetton Rugby', 'Bulls', 'Cardiff Rugby', 'Connacht Rugby', 'Dragons RFC',
                'Edinburgh Rugby', 'Glasgow Warriors', 'Leinster Rugby', 'Lions', 'Munster Rugby',
                'Ospreys', 'Scarlets', 'Sharks', 'Stormers', 'Ulster Rugby', 'Zebre Parma',
            ],
            'American football team in the 2026 NFL.' => [
                'Arizona Cardinals', 'Atlanta Falcons', 'Baltimore Ravens', 'Buffalo Bills',
                'Carolina Panthers', 'Chicago Bears', 'Cincinnati Bengals', 'Cleveland Browns',
                'Dallas Cowboys', 'Denver Broncos', 'Detroit Lions', 'Green Bay Packers', 'Houston Texans',
                'Indianapolis Colts', 'Jacksonville Jaguars', 'Kansas City Chiefs', 'Las Vegas Raiders',
                'Los Angeles Chargers', 'Los Angeles Rams', 'Miami Dolphins', 'Minnesota Vikings',
                'New England Patriots', 'New Orleans Saints', 'New York Giants', 'New York Jets',
                'Philadelphia Eagles', 'Pittsburgh Steelers', 'San Francisco 49ers', 'Seattle Seahawks',
                'Tampa Bay Buccaneers', 'Tennessee Titans', 'Washington Commanders',
            ],
            'Touch rugby representative at the 2024 Touch World Cup.' => [
                'Australia Touch', 'Belgium Touch', 'Canada Touch', 'Cayman Islands Touch', 'Chile Touch',
                'China Touch', 'Chinese Taipei Touch', 'Cook Islands Touch', 'England Touch', 'Fiji Touch',
                'France Touch', 'Germany Touch', 'Guernsey Touch', 'Hong Kong China Touch', 'Ireland Touch',
                'Italy Touch', 'Japan Touch', 'Jersey Touch', 'Lebanon Touch', 'Luxembourg Touch',
                'Malaysia Touch', 'Middle East Touch', 'Netherlands Touch', 'New Zealand Touch', 'Oman Touch',
                'Papua New Guinea Touch', 'Philippines Touch', 'Portugal Touch', 'Samoa Touch', 'Scotland Touch',
                'Singapore Touch', 'South Africa Touch', 'Spain Touch', 'Sweden Touch', 'Switzerland Touch',
                'Thailand Touch', 'Tonga Touch', 'United States Touch', 'Wales Touch',
            ],
        ];

        $users = User::query()->orderBy('email')->pluck('id');
        $userIndex = 0;

        foreach ($competitions as $description => $clubs) {
            foreach ($clubs as $name) {
                $club = Club::withTrashed()->firstOrCreate(['name' => $name], ['description' => $description]);

                if ($users->isEmpty()) {
                    continue;
                }

                $existingUserIds = $club->users()->pluck('users.id');
                $assignments = [];

                for ($i = 0; $i < min(25, $users->count()); $i++) {
                    $userId = $users[$userIndex % $users->count()];
                    $userIndex++;

                    if (! $existingUserIds->contains($userId)) {
                        $assignments[$userId] = ['permissions' => []];
                    }
                }

                $club->users()->attach($assignments);
            }
        }
    }
}
