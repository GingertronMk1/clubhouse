<?php

namespace Database\Seeders;

use App\Models\Sport;
use Illuminate\Database\Seeder;

class SportSeeder extends Seeder
{
    public function run(): void
    {
        $sports = [
            [
                'name' => 'Rugby League',
                'description' => 'A full-contact rugby football code played by teams of thirteen.',
                'scoring' => [
                    ['name' => 'Try', 'value' => 4],
                    ['name' => 'Conversion', 'value' => 2],
                    ['name' => 'Penalty goal', 'value' => 2],
                    ['name' => 'Drop goal', 'value' => 1],
                ],
            ],
            [
                'name' => 'Rugby Union',
                'description' => 'A full-contact rugby football code traditionally played by teams of fifteen.',
                'scoring' => [
                    ['name' => 'Try', 'value' => 5],
                    ['name' => 'Conversion', 'value' => 2],
                    ['name' => 'Penalty try', 'value' => 7],
                    ['name' => 'Penalty goal', 'value' => 3],
                    ['name' => 'Drop goal', 'value' => 3],
                ],
            ],
            [
                'name' => 'Touch Rugby',
                'description' => 'A rugby-derived sport in which a touch replaces a tackle.',
                'scoring' => [
                    ['name' => 'Touchdown', 'value' => 1],
                ],
            ],
            [
                'name' => 'American Football',
                'description' => 'A team sport in which players advance the ball into the opposing end zone.',
                'scoring' => [
                    ['name' => 'Touchdown', 'value' => 6],
                    ['name' => 'Field goal', 'value' => 3],
                    ['name' => 'Safety', 'value' => 2],
                    ['name' => 'Extra point', 'value' => 1],
                    ['name' => 'Two-point conversion', 'value' => 2],
                ],
            ],
        ];

        foreach ($sports as $sport) {
            Sport::query()->updateOrCreate(['name' => $sport['name']], $sport);
        }
    }
}
