<?php

namespace Database\Seeders;

use App\Models\Sport;
use Illuminate\Database\Seeder;

class ScoringEventSeeder extends Seeder
{
    public function run(): void
    {
        $sports = [
            'Rugby League' => [['Try', 4], ['Conversion', 2], ['Penalty goal', 2], ['Drop goal', 1]],
            'Rugby Union' => [['Try', 5], ['Conversion', 2], ['Penalty try', 7], ['Penalty goal', 3], ['Drop goal', 3]],
            'Touch Rugby' => [['Touchdown', 1]],
            'American Football' => [['Touchdown', 6], ['Field goal', 3], ['Safety', 2], ['Extra point', 1], ['Two-point conversion', 2]],
        ];

        foreach ($sports as $sportName => $events) {
            $sport = Sport::query()->where('name', $sportName)->firstOrFail();
            foreach ($events as $sortOrder => [$name, $value]) {
                $sport->scoringEvents()->updateOrCreate(['name' => $name], ['value' => $value, 'sort_order' => $sortOrder]);
            }
        }
    }
}
