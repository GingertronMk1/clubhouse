<?php

namespace Database\Seeders;

use App\Models\Sport;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->sportPositions() as ['sport' => $sportName, 'positions' => $positions]) {
            $sport = Sport::query()->firstOrCreate(['name' => $sportName]);
            $sport->positions()->createMany($positions);
        }
    }

    public function sportPositions(): array
    {
        return [
            [
                'sport' => 'Rugby League',
                'positions' => [
                    [
                        'name' => 'Fullback',
                        'default_number' => 1,
                    ],
                    [
                        'name' => 'Left Wing',
                        'default_number' => 2,
                    ],
                    [
                        'name' => 'Left Centre',
                        'default_number' => 3,
                    ],
                    [
                        'name' => 'Right Centre',
                        'default_number' => 4,
                    ],
                    [
                        'name' => 'Right Wing',
                        'default_number' => 5,
                    ],
                    [
                        'name' => 'Stand-Off',
                        'default_number' => 6,
                    ],
                    [
                        'name' => 'Scrum-Half',
                        'default_number' => 7,
                    ],
                    [
                        'name' => 'Left Prop',
                        'default_number' => 8,
                    ],
                    [
                        'name' => 'Hooker',
                        'default_number' => 9,
                    ],
                    [
                        'name' => 'Right Prop',
                        'default_number' => 10,
                    ],
                    [
                        'name' => 'Left Second-Row',
                        'default_number' => 11,
                    ],
                    [
                        'name' => 'Right Second-Row',
                        'default_number' => 12,
                    ],
                    [
                        'name' => 'Loose-Forward',
                        'default_number' => 13,
                    ],
                ],
            ],
            [
                'sport' => 'Rugby Union',
                'positions' => [
                    [
                        'name' => 'Loosehead Prop',
                        'default_number' => 1,
                    ],
                    [
                        'name' => 'Hooker',
                        'default_number' => 2,
                    ],
                    [
                        'name' => 'Tighthead Prop',
                        'default_number' => 3,
                    ],
                    [
                        'name' => 'Loosehead Lock',
                        'default_number' => 4,
                    ],
                    [
                        'name' => 'Tighthead Lock',
                        'default_number' => 5,
                    ],
                    [
                        'name' => 'Blindside Flanker',
                        'default_number' => 6,
                    ],
                    [
                        'name' => 'Openside Flanker',
                        'default_number' => 7,
                    ],
                    [
                        'name' => 'Number 8',
                        'default_number' => 8,
                    ],
                    [
                        'name' => 'Scrum-Half',
                        'default_number' => 9,
                    ],
                    [
                        'name' => 'Fly-Half',
                        'default_number' => 10,
                    ],
                    [
                        'name' => 'Left Wing',
                        'default_number' => 11,
                    ],
                    [
                        'name' => 'Inside Centre',
                        'default_number' => 12,
                    ],
                    [
                        'name' => 'Outside Centre',
                        'default_number' => 13,
                    ],
                    [
                        'name' => 'Right Wing',
                        'default_number' => 14,
                    ],
                    [
                        'name' => 'Fullback',
                        'default_number' => 15,
                    ],
                ],
            ],
            [
                'sport' => 'Touch Rugby',
                'positions' => [
                    [
                        'name' => 'Wing',
                        'default_number' => 1,
                    ],
                    [
                        'name' => 'Link',
                        'default_number' => 2,
                    ],
                    [
                        'name' => 'Mid',
                        'default_number' => 3,
                    ],
                ],
            ],
            [
                'sport' => 'American Football',
                'positions' => [
                    [
                        'name' => 'Quarterback',
                    ],
                    [
                        'name' => 'Fullback',
                    ],
                    [
                        'name' => 'Halfback',
                    ],
                    [
                        'name' => 'Tight End',
                    ],
                    [
                        'name' => 'Left Tackle',
                    ],
                    [
                        'name' => 'Left Guard',
                    ],
                    [
                        'name' => 'Center',
                    ],
                    [
                        'name' => 'Right Guard',
                    ],
                    [
                        'name' => 'Right Tackle',
                    ],
                    [
                        'name' => 'Free Safety',
                    ],
                    [
                        'name' => 'Strong Safety',
                    ],
                    [
                        'name' => 'Cornerback',
                    ],
                    [
                        'name' => 'Left Outside Linebacker',
                    ],
                    [
                        'name' => 'Middle Linebacker',
                    ],
                    [
                        'name' => 'Right Outside Linebacker',
                    ],
                    [
                        'name' => 'Left Defensive End',
                    ],
                    [
                        'name' => 'Defensive Tackle',
                    ],
                    [
                        'name' => 'Right Defensive End',
                    ],
                    [
                        'name' => 'Kicker',
                    ],
                    [
                        'name' => 'Punter',
                    ],
                ],
            ],
        ];
    }
}
