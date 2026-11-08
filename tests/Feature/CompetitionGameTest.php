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
    $team1 = Team::factory()->create();
    $team2 = Team::factory()->create();
    $location = Location::factory()->create();
    $game = Game::factory()->for($sport)->for($competition)->for($team1, 'team1')->for($team2, 'team2')->for($location)->create([
        'start' => '2026-11-08 12:30:00',
        'score' => [],
    ])->fresh();

    expect($game->sport->is($sport))->toBeTrue();
    expect($game->competition->is($competition))->toBeTrue();
    expect($game->team1->is($team1))->toBeTrue();
    expect($game->team2->is($team2))->toBeTrue();
    expect($game->location->is($location))->toBeTrue();
    expect($game->start)->toBeInstanceOf(CarbonInterface::class)->format('Y-m-d H:i:s')->toBe('2026-11-08 12:30:00');
    expect($game->score)->toBe([]);
});
