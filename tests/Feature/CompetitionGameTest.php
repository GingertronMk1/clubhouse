<?php

use App\Models\Competition;
use App\Models\Game;
use App\Models\Location;
use App\Models\Sport;
use App\Models\Team;
use Carbon\CarbonInterface;

test('competitions can have a parent, children, a location and games', function () {
    $parent = Competition::factory()->create();
    $location = Location::factory()->create();
    $competition = Competition::factory()->for($parent, 'parent')->for($location)->create();
    $game = Game::factory()->for($competition)->create();

    expect($competition->parent->is($parent))->toBeTrue();
    expect($parent->children->contains($competition))->toBeTrue();
    expect($competition->location->is($location))->toBeTrue();
    expect($competition->games->contains($game))->toBeTrue();
});

test('games retain their related records and cast the start and score', function () {
    $sport = Sport::factory()->create();
    $competition = Competition::factory()->create();
    $club1 = Team::factory()->create();
    $club2 = Team::factory()->create();
    $location = Location::factory()->create();
    $game = Game::factory()->for($sport)->for($competition)->for($club1, 'club1')->for($club2, 'club2')->for($location)->create([
        'start' => '2026-11-08 12:30:00',
        'score' => ['club1' => 2, 'club2' => 1],
    ])->fresh();

    expect($game->sport->is($sport))->toBeTrue();
    expect($game->competition->is($competition))->toBeTrue();
    expect($game->club1->is($club1))->toBeTrue();
    expect($game->club2->is($club2))->toBeTrue();
    expect($game->location->is($location))->toBeTrue();
    expect($game->start)->toBeInstanceOf(CarbonInterface::class)->format('Y-m-d H:i:s')->toBe('2026-11-08 12:30:00');
    expect($game->score)->toBe(['club1' => 2, 'club2' => 1]);
});
