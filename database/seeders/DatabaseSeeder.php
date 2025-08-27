<?php

namespace Database\Seeders;

use App\Models\Competition;
use App\Models\Game;
use App\Models\Person;
use App\Models\Position;
use App\Models\Sport;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (app()->isLocal()) {
            User::factory()->create([
                'name' => 'Admin',
                'email' => 'test@example.com',
                'password' => bcrypt(1234),
            ]);
        }

        $this->command->info('Creating people');
        Person::factory(30)->create();
        $this->command->info('Creating sports');
        Sport::factory(10)->create();
        $this->command->info('Creating teams');
        Team::factory(10)->create();

        Sport::query()->each(function (Sport $sport, int $n) {
            $this->command->info("Creating competitions for {$sport->name}");
            Competition::factory(10)->create(['sport_id' => $sport->id]);
            $this->command->info("Creating positions for {$sport->name}");
            Position::factory(($n % 10) + 1)->create(['sport_id' => $sport->id]);
        });

        Competition::query()->each(function (Competition $competition) {
            $this->command->info("Creating games for {$competition->name}");
            Game::factory(10)->create(['competition_id' => $competition->id]);
        });

        $this->command->info('Attaching people to teams');
        $this->command->withProgressBar(
            Team::query()->cursor(),
            function (Team $team) {
                $team->people()->attach(
                    Person::query()
                        ->inRandomOrder()
                        ->limit(25)
                        ->pluck('id')
                );
            });

        $this->command->info('Attaching things to games');
        $this->command->withProgressBar(
            Game::query()->cursor(),
            function (Game $game) {
                $teams = Team::query()
                    ->inRandomOrder()
                    ->limit(2)
                    ->get();
                $game->teams()->attach($teams->pluck('id'));
                $teams->each(function (Team $team) use ($game) {
                    $team->people()->each(function (Person $person) use ($team, $game) {
                        $game->players()->attach($person->id, [
                            'position_id' => $game->sport->positions()->inRandomOrder()->value('id'),
                            'team_id' => $team->id,
                        ]);
                    });
                });
            });
    }
}
