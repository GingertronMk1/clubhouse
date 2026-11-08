<?php

use App\Models\Game;
use App\Models\GameScoringEvent;
use App\Models\ScoringEvent;
use App\Models\Team;
use Inertia\Testing\AssertableInertia as Assert;

function gameScoringInput(Game $game, array $events): array
{
    return ['name' => $game->name, 'start' => '2026-11-08T12:30', 'description' => $game->description, 'summary' => $game->summary, 'sport_id' => $game->sport_id, 'competition_id' => $game->competition_id, 'club1_id' => $game->club1_id, 'club2_id' => $game->club2_id, 'location_id' => $game->location_id, 'scoring_events' => $events];
}

test('games can record display edit and remove scoring events for each team', function () {
    $this->withoutVite();
    $sample = Game::factory()->create(['start' => '2026-01-01 00:00:00']);
    $try = ScoringEvent::factory()->for($sample->sport)->create(['name' => 'Try', 'value' => 4]);
    $goal = ScoringEvent::factory()->for($sample->sport)->create(['name' => 'Goal', 'value' => 2]);
    $events = [['scoring_event_id' => $try->id, 'team_id' => $sample->club1_id, 'timestamp' => 0], ['scoring_event_id' => $goal->id, 'team_id' => $sample->club2_id, 'timestamp' => 125]];

    $this->post(route('games.store'), gameScoringInput($sample, $events))->assertRedirect();
    $game = Game::query()->whereKeyNot($sample->id)->firstOrFail();
    $first = $game->scoringEvents()->where('team_id', $game->club1_id)->firstOrFail();
    $second = $game->scoringEvents()->where('team_id', $game->club2_id)->firstOrFail();
    $this->get(route('games.show', $game))->assertInertia(fn (Assert $page) => $page->where('scoreTotals.club1', 4)->where('scoreTotals.club2', 2)->has('record.scoring_events', 2)->where('record.scoring_events.0.timestamp', 0));
    $this->get(route('games.index'))->assertInertia(fn (Assert $page) => $page->has('records', 2)->where('records.0.score_totals', null)->where('records.1.score_totals.club1', 4));
    $this->get(route('games.edit', $game))->assertInertia(fn (Assert $page) => $page->has('record.scoring_events', 2)->has('sports.0.scoring_events', 2));

    $this->put(route('games.update', $game), gameScoringInput($game, [['id' => $first->id, 'scoring_event_id' => $try->id, 'team_id' => $game->club1_id, 'timestamp' => null]]))->assertRedirect();
    expect($first->fresh()->timestamp)->toBeNull();
    $this->assertSoftDeleted($second);
    expect($game->scoringEvents()->count())->toBe(1);
});

test('scoring rejects other games events wrong sports teams and invalid elapsed times', function () {
    $game = Game::factory()->create();
    $otherGame = Game::factory()->create();
    $event = GameScoringEvent::factory()->for($otherGame)->create();
    $wrongType = ScoringEvent::factory()->create();
    $otherTeam = Team::factory()->create();
    $events = [['id' => $event->id, 'scoring_event_id' => $wrongType->id, 'team_id' => $otherTeam->id, 'timestamp' => -1]];

    $this->put(route('games.update', $game), gameScoringInput($game, $events))->assertSessionHasErrors(['scoring_events.0.id', 'scoring_events.0.scoring_event_id', 'scoring_events.0.team_id', 'scoring_events.0.timestamp']);
    expect($game->scoringEvents()->count())->toBe(0);
    $this->assertNotSoftDeleted($event);
});

test('new events require a team and cannot use archived scoring types', function () {
    $game = Game::factory()->create();
    $type = ScoringEvent::factory()->for($game->sport)->create();
    $type->delete();

    $this->put(route('games.update', $game), gameScoringInput($game, [['scoring_event_id' => $type->id, 'team_id' => null, 'timestamp' => null]]))->assertSessionHasErrors(['scoring_events.0.team_id', 'scoring_events.0.scoring_event_id']);
    expect($game->scoringEvents()->count())->toBe(0);
});

test('existing unassigned and archived events remain visible and can be retained', function () {
    $this->withoutVite();
    $game = Game::factory()->create();
    $type = ScoringEvent::factory()->for($game->sport)->create(['name' => 'Try', 'value' => 4]);
    $event = GameScoringEvent::factory()->for($game)->for($type)->create(['team_id' => null, 'timestamp' => null]);
    $type->delete();
    $assignedType = ScoringEvent::factory()->for($game->sport)->create(['value' => 2]);
    GameScoringEvent::factory()->for($game)->for($assignedType)->create(['team_id' => $game->club1_id]);

    $this->get(route('games.show', $game))->assertInertia(fn (Assert $page) => $page->where('scoreTotals', null)->where('record.scoring_events.0.scoring_event.name', 'Try')->where('record.scoring_events.0.team_id', null));
    $this->put(route('games.update', $game), gameScoringInput($game, [['id' => $event->id, 'scoring_event_id' => $type->id, 'team_id' => null, 'timestamp' => 0]]))->assertRedirect();
    expect($event->fresh()->timestamp)->toBe(0);
});

test('changing sport or teams without supplying existing events is rejected', function () {
    $game = Game::factory()->create();
    GameScoringEvent::factory()->for($game)->create(['team_id' => $game->club1_id]);
    $otherTeam = Team::factory()->create();
    $data = gameScoringInput($game, []);
    unset($data['scoring_events']);
    $data['club1_id'] = $otherTeam->id;

    $this->put(route('games.update', $game), $data)->assertSessionHasErrors('scoring_events');
    expect($game->refresh()->club1_id)->not->toBe($otherTeam->id);
});

test('updating game details without scoring data preserves existing events', function () {
    $game = Game::factory()->create();
    $event = GameScoringEvent::factory()->for($game)->create(['team_id' => $game->club1_id]);
    $data = gameScoringInput($game, []);
    unset($data['scoring_events']);
    $data['name'] = 'Updated game';

    $this->put(route('games.update', $game), $data)->assertRedirect();

    expect($game->refresh()->name)->toBe('Updated game');
    $this->assertNotSoftDeleted($event);
});

test('the team migration preserves existing unassigned scoring events', function () {
    $game = Game::factory()->create();
    $event = GameScoringEvent::factory()->for($game)->create(['timestamp' => 42]);
    $migration = require database_path('migrations/2026_11_08_175922_add_team_to_game_scoring_events_table.php');

    $migration->down();
    $migration->up();

    expect($event->fresh()->timestamp)->toBe(42);
    expect($event->fresh()->team_id)->toBeNull();
});
