<?php

use App\Models\Competition;
use App\Models\Game;
use Database\Seeders\ClubSeeder;
use Database\Seeders\CompetitionSeeder;
use Database\Seeders\GameSeeder;
use Database\Seeders\SportSeeder;
use Database\Seeders\TeamSeeder;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;

test('competition and game seeders import the fixtures and can be repeated', function () {
    $this->seed([SportSeeder::class, ClubSeeder::class, TeamSeeder::class, CompetitionSeeder::class, GameSeeder::class]);

    $counts = ['2026 NFL season' => 272, '2026 NRL season' => 213, '2026 Super League season' => 194, '2024 Touch World Cup' => 214, '2025–26 United Rugby Championship season' => 151, '2025–26 Premiership Rugby season' => 93];
    foreach ($counts as $name => $count) {
        $competition = Competition::query()->where('name', $name)->firstOrFail();
        expect($competition->games()->count())->toBe($count);
    }

    $fixtures = json_decode(file_get_contents(database_path('seeders/data/competition-fixtures.json')), true, flags: JSON_THROW_ON_ERROR);
    $fixtureIds = array_column($fixtures, 'id');
    expect($fixtureIds)->toHaveCount(array_sum($counts));
    expect(array_unique($fixtureIds))->toHaveCount(array_sum($counts));
    expect(collect($fixtureIds)->every(fn (string $id): bool => Str::isUuid($id)))->toBeTrue();
    expect(Game::query()->pluck('id')->sort()->values()->all())->toBe(collect($fixtureIds)->sort()->values()->all());

    $game = Game::query()->where('description', 'nfl fixture 1')->firstOrFail();
    expect($game->team1->name)->toBe('First team');
    expect($game->team1->club->name)->toBe('Seattle Seahawks');
    expect($game->team2->name)->toBe('First team');
    expect($game->team2->club->name)->toBe('New England Patriots');
    expect($game->location->name)->toBe('Lumen Field');
    expect($game->start->format('Y-m-d H:i:s'))->toBe('2026-09-10 00:20:00');
    expect($game->score)->toBe([]);
    $game->update(['summary' => 'Custom summary', 'score' => ['home' => 2, 'away' => 1]]);

    $touch = Game::query()->where('description', 'touch fixture 7524')->firstOrFail();
    expect($touch->team1->name)->toBe("Men's Open");
    expect($touch->team2->name)->toBe("Men's Open");
    expect($touch->start->format('Y-m-d H:i:s'))->toBe('2024-07-16 11:20:00');
    expect(Game::query()->whereNull('start')->count())->toBe(24);

    $urc = Game::query()->where('description', 'urc fixture 1')->firstOrFail();
    expect($urc->competition->id)->toBe('1283f3b2-ee8f-4ad7-929f-f0bc3631ece5');
    expect($urc->sport->name)->toBe('Rugby Union');
    expect($urc->team1->club->name)->toBe('Stormers');
    expect($urc->team2->club->name)->toBe('Leinster Rugby');
    expect($urc->start->format('Y-m-d H:i:s'))->toBe('2025-09-26 17:00:00');
    expect($urc->location->name)->toBe('DHL Stadium, Cape Town, South Africa');
    expect($urc->location->id)->toBe('542eeade-5241-448c-b36a-a210cf4dc67d');

    $premFinal = Game::query()->where('description', 'prem fixture 93')->firstOrFail();
    expect($premFinal->competition->id)->toBe('4d69e5e0-70b6-4b94-999e-79b16699ba3d');
    expect($premFinal->sport->name)->toBe('Rugby Union');
    expect($premFinal->team1->club->name)->toBe('Northampton Saints');
    expect($premFinal->team2->club->name)->toBe('Exeter Chiefs');
    expect($premFinal->start->format('Y-m-d H:i:s'))->toBe('2026-06-20 14:00:00');
    expect($premFinal->summary)->toBe('First team — Final');

    $this->seed([CompetitionSeeder::class, GameSeeder::class]);

    expect(Competition::query()->count())->toBe(6);
    expect(Game::query()->count())->toBe(array_sum($counts));
    expect($game->fresh()->summary)->toBe('Custom summary');
    expect($game->fresh()->score)->toBe(['home' => 2, 'away' => 1]);
});

test('games without an announced kickoff can be viewed and edited', function () {
    $this->withoutVite();
    $game = Game::factory()->create(['start' => null]);

    $this->get(route('games.show', $game))->assertInertia(fn (Assert $page) => $page->component('games/Show')->where('record.start', null));
    $this->get(route('games.edit', $game))->assertInertia(fn (Assert $page) => $page->component('games/Form')->where('start', null));
});
