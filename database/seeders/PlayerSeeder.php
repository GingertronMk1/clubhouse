<?php

namespace Database\Seeders;

use App\Models\Club;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clubs = Storage::json('fixtures/clubs.json');
        Club::query()->get()->each(function (Club $club) use ($clubs) {
            if (!isset($clubs[$club->name])) {
                $this->command->newLine("No club for {$club->name}.");
                return;
            }
            $players = $clubs[$club->name];
            $club->players()->createMany(array_map(fn (string $player) => ['name' => $player], $players));
        });
    }
}
