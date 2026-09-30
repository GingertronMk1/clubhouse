<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\Competition;
use App\Models\Game;
use App\Models\Location;
use App\Models\Position;
use App\Models\Sport;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 1234,
        ]);

        $this->command->info('Creating users');
        User::factory(100)->create();

        $this->command->info('Creating sports');
        Sport::factory(10)->create();
        $this->command->info('Creating locations');
        Location::factory(10)->create();
        $this->command->info('Creating teams');
        Team::factory(50)->create();
        $this->command->info('Assigning positions to sports');
        Sport::query()->each(function (Sport $sport) {
            $sport->positions()->saveMany(Position::factory(10)->make());
        });

        $this->command->info('Assigning clubs and users to teams');
        Team::query()->each(function (Team $team) {
            $team->clubs()->saveMany(Club::factory(10)->make());
            $team
                ->users()
                ->attach(
                    User::query()
                        ->inRandomOrder()
                        ->limit(20)
                        ->pluck('id')
                );
        });

        $this->command->info('Creating competitions');
        Competition::factory(20)->create();
        $this->command->info('Creating games');
        Game::factory(500)->create();

        $this->command->info('Assigning players to games');
        $this->command->withProgressBar(Game::query()->get()->all(), function (Game $game) {
            $game->sport->load('positions')->positions->each(function (Position $position) use ($game) {
                foreach ([$game->club1, $game->club2] as $club) {
                    for ($i = 0; $i < $position->per_side; $i++) {
                        $game->players()->attach(
                            $club->team->users()->inRandomOrder()->first(),
                            [
                                'position_id' => $position->id,
                                'club_id' => $club->id,
                            ]
                        );
                    }
                }
            });
        });
        $this->command->newLine(2);
    }
}
