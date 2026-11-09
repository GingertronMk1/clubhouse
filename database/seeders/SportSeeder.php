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
                'events' => [
                    ['name' => 'Try', 'score' => 4],
                    ['name' => 'Conversion', 'score' => 2],
                    ['name' => 'Penalty goal', 'score' => 2],
                    ['name' => 'Drop goal', 'score' => 1],
                ],
            ],
            [
                'name' => 'Rugby Union',
                'description' => 'A full-contact rugby football code traditionally played by teams of fifteen.',
                'events' => [
                    ['name' => 'Try', 'score' => 5],
                    ['name' => 'Conversion', 'score' => 2],
                    ['name' => 'Penalty try', 'score' => 7],
                    ['name' => 'Penalty goal', 'score' => 3],
                    ['name' => 'Drop goal', 'score' => 3],
                ],
            ],
            [
                'name' => 'Touch Rugby',
                'description' => 'A rugby-derived sport in which a touch replaces a tackle.',
                'events' => [
                    ['name' => 'Touchdown', 'score' => 1],
                ],
            ],
            [
                'name' => 'American Football',
                'description' => 'A team sport in which players advance the ball into the opposing end zone.',
                'events' => [
                    ['name' => 'Touchdown', 'score' => 6],
                    ['name' => 'Field goal', 'score' => 3],
                    ['name' => 'Safety', 'score' => 2],
                    ['name' => 'Extra point', 'score' => 1],
                    ['name' => 'Two-point conversion', 'score' => 2],
                ],
            ],
        ];

        foreach ($sports as $sport) {
            $events = $sport['events'];
            unset($sport['events']);
            $model = Sport::query()->updateOrCreate(['name' => $sport['name']], $sport);

            foreach ($events as $event) {
                $model->events()->updateOrCreate(['name' => $event['name']], ['description' => '', 'score' => $event['score']]);
            }
        }
    }
}
