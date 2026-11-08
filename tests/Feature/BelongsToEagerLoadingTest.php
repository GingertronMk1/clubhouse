<?php

use App\Models\Competition;
use App\Models\Game;
use App\Models\Position;
use App\Models\Team;

test('teams and positions load their parent records by default', function () {
    $team = Team::factory()->create();
    $position = Position::factory()->create();

    $loadedTeam = Team::query()->findOrFail($team->id);
    $loadedPosition = Position::query()->findOrFail($position->id);

    expect($loadedTeam->relationLoaded('club'))->toBeTrue();
    expect($loadedTeam->club->id)->toBe($team->club_id);
    expect($loadedPosition->relationLoaded('sport'))->toBeTrue();
    expect($loadedPosition->sport->id)->toBe($position->sport_id);
});

test('competitions load their location and parent hierarchy by default', function () {
    $grandparent = Competition::factory()->create();
    $parent = Competition::factory()->for($grandparent, 'parent')->create();
    $competition = Competition::factory()->for($parent, 'parent')->create();

    $loaded = Competition::query()->findOrFail($competition->id);

    expect($loaded->relationLoaded('location'))->toBeTrue();
    expect($loaded->location->id)->toBe($competition->location_id);
    expect($loaded->relationLoaded('parent'))->toBeTrue();
    expect($loaded->parent->id)->toBe($parent->id);
    expect($loaded->parent->relationLoaded('parent'))->toBeTrue();
    expect($loaded->parent->parent->id)->toBe($grandparent->id);
    expect($loaded->parent->parent->parent)->toBeNull();
});

test('games load all of their belongs to records by default', function () {
    $game = Game::factory()->create();

    $loaded = Game::query()->findOrFail($game->id);

    foreach (['sport', 'competition', 'team1', 'team2', 'location'] as $relationship) {
        expect($loaded->relationLoaded($relationship))->toBeTrue();
        expect($loaded->{$relationship}->id)->toBe($game->{$relationship.'_id'});
    }
});
