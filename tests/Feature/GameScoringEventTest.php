<?php

use App\Models\Game;
use App\Models\GameScoringEvent;
use App\Models\ScoringEvent;
use Database\Seeders\GameScoringEventSeeder;
use Illuminate\Support\Str;

test('game scoring events belong to a game and scoring event and can have an elapsed timestamp', function () {
    $game = Game::factory()->create();
    $scoringEvent = ScoringEvent::factory()->for($game->sport)->create();
    $event = GameScoringEvent::factory()->for($game)->for($scoringEvent)->create(['timestamp' => 125]);
    $withoutTimestamp = GameScoringEvent::factory()->for($game)->for($scoringEvent)->create(['timestamp' => null]);
    $atKickoff = GameScoringEvent::factory()->for($game)->for($scoringEvent)->create(['timestamp' => 0]);

    $loaded = GameScoringEvent::query()->findOrFail($event->id);

    expect(Str::isUuid($loaded->id, version: 7))->toBeTrue();
    expect($loaded->relationLoaded('game'))->toBeTrue();
    expect($loaded->relationLoaded('scoringEvent'))->toBeTrue();
    expect($loaded->game->is($game))->toBeTrue();
    expect($loaded->scoringEvent->is($scoringEvent))->toBeTrue();
    expect($loaded->timestamp)->toBe(125);
    expect($withoutTimestamp->fresh()->timestamp)->toBeNull();
    expect($atKickoff->fresh()->timestamp)->toBe(0);
    expect($game->scoringEvents()->count())->toBe(3);
    expect($scoringEvent->gameScoringEvents()->count())->toBe(3);

    $event->delete();
    $this->assertSoftDeleted($event);
    expect($game->scoringEvents()->count())->toBe(2);
});

test('the factory creates a scoring event for the game sport', function () {
    $event = GameScoringEvent::factory()->create();

    expect($event->scoringEvent->sport->is($event->game->sport))->toBeTrue();
    expect($event->timestamp)->toBeNull();
});

test('game scoring event seeding leaves fixture scorelines empty', function () {
    $game = Game::factory()->create();

    $this->seed(GameScoringEventSeeder::class);

    expect($game->scoringEvents()->count())->toBe(0);
});
