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
            ],
            [
                'name' => 'Rugby Union',
                'description' => 'A full-contact rugby football code traditionally played by teams of fifteen.',
            ],
            [
                'name' => 'Touch Rugby',
                'description' => 'A rugby-derived sport in which a touch replaces a tackle.',
            ],
            [
                'name' => 'American Football',
                'description' => 'A team sport in which players advance the ball into the opposing end zone.',
            ],
        ];

        foreach ($sports as $sport) {
            Sport::query()->updateOrCreate(['name' => $sport['name']], $sport);
        }

        $this->call(ScoringEventSeeder::class);
    }
}
