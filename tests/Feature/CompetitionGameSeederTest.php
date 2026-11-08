<?php

use App\Models\Competition;
use App\Models\Game;
use Database\Seeders\ClubSeeder;
use Database\Seeders\CompetitionSeeder;
use Database\Seeders\GameSeeder;
use Database\Seeders\SportSeeder;
use Database\Seeders\TeamSeeder;
use Inertia\Testing\AssertableInertia as Assert;

test('competition and game seeders import the fixtures and can be repeated', function () {
    $this->seed([SportSeeder::class, ClubSeeder::class, TeamSeeder::class, CompetitionSeeder::class, GameSeeder::class]);

    $counts = ['2026 NFL season' => 272, '2026 NRL season' => 213, '2026 Super League season' => 194, '2024 Touch World Cup' => 214];
    foreach ($counts as $name => $count) {
        $competition = Competition::query()->where('name', $name)->firstOrFail();
        expect($competition->games()->count())->toBe($count);
    }

    $game = Game::query()->where('description', 'nfl fixture 1')->firstOrFail();
    expect($game->club1->club->name)->toBe('Seattle Seahawks');
    expect($game->club2->club->name)->toBe('New England Patriots');
    expect($game->location->name)->toBe('Lumen Field');
    expect($game->start->format('Y-m-d H:i:s'))->toBe('2026-09-10 00:20:00');
    $game->update(['summary' => 'Custom summary']);

    $touch = Game::query()->where('description', 'touch fixture 7524')->firstOrFail();
    expect($touch->club1->name)->toBe("Men's Open");
    expect($touch->club2->name)->toBe("Men's Open");
    expect($touch->start->format('Y-m-d H:i:s'))->toBe('2024-07-16 11:20:00');
    expect(Game::query()->whereNull('start')->count())->toBe(24);

    $this->seed([CompetitionSeeder::class, GameSeeder::class]);

    expect(Competition::query()->count())->toBe(4);
    expect(Game::query()->count())->toBe(array_sum($counts));
    expect($game->fresh()->summary)->toBe('Custom summary');
});

test('games without an announced kickoff can be viewed and edited', function () {
    $this->withoutVite();
    $game = Game::factory()->create(['start' => null]);

    $this->get(route('games.show', $game))->assertInertia(fn (Assert $page) => $page->component('games/Show')->where('record.start', null));
    $this->get(route('games.edit', $game))->assertInertia(fn (Assert $page) => $page->component('games/Form')->where('start', null));
});
