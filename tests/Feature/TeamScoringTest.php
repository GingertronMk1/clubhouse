<?php

use App\Models\Game;
use App\Models\Sport;
use Inertia\Testing\AssertableInertia as Assert;

test('each team can record counts for the selected sport scoring methods', function () {
    $this->withoutVite();
    $sport = Sport::factory()->create(['scoring' => [['name' => 'Try', 'value' => 4], ['name' => 'Conversion', 'value' => 2]]]);
    $game = Game::factory()->for($sport)->create(['result' => ['home' => 18, 'away' => 12]]);
    $data = ['name' => $game->name, 'start' => '2026-11-08T12:30', 'description' => $game->description, 'summary' => $game->summary, 'sport_id' => $sport->id, 'competition_id' => $game->competition_id, 'club1_id' => $game->club1_id, 'club2_id' => $game->club2_id, 'location_id' => $game->location_id, 'club1_score' => [['name' => 'Try', 'value' => 4, 'count' => 3], ['name' => 'Conversion', 'value' => 2, 'count' => 3]], 'club2_score' => [['name' => 'Try', 'value' => 4, 'count' => 3]]];

    $this->put(route('games.update', $game), $data)->assertRedirect(route('games.show', $game));

    expect($game->refresh()->club1_score)->toBe($data['club1_score']);
    expect($game->club2_score)->toBe($data['club2_score']);
    expect($game->result)->toBe(['home' => 18, 'away' => 12]);
    $this->get(route('games.show', $game))->assertInertia(fn (Assert $page) => $page->where('record.club1_score.0.count', 3)->where('record.club2_score.0.count', 3));
});

test('scoring rejects methods outside the sport and invalid counts', function () {
    $sport = Sport::factory()->create(['scoring' => [['name' => 'Try', 'value' => 4]]]);
    $game = Game::factory()->for($sport)->create();
    $data = ['name' => $game->name, 'start' => '2026-11-08T12:30', 'description' => $game->description, 'summary' => $game->summary, 'sport_id' => $sport->id, 'competition_id' => $game->competition_id, 'club1_id' => $game->club1_id, 'club2_id' => $game->club2_id, 'location_id' => $game->location_id, 'club1_score' => [['name' => 'Try', 'value' => 6, 'count' => -1]], 'club2_score' => [['name' => 'Goal', 'value' => 4, 'count' => 1]]];

    $this->put(route('games.update', $game), $data)->assertSessionHasErrors(['club1_score.0.name', 'club1_score.0.count', 'club2_score.0.name']);

    expect($game->refresh()->club1_score)->toBe([]);
    expect($game->club2_score)->toBe([]);
});

test('the scoring migration preserves previously recorded results', function () {
    $game = Game::factory()->create(['result' => ['home' => 13, 'away' => 10]]);
    $migration = require database_path('migrations/2026_11_08_170126_add_team_scoring_to_games_table.php');

    $migration->down();
    $migration->up();

    expect($game->fresh()->result)->toBe(['home' => 13, 'away' => 10]);
    expect($game->fresh()->club1_score)->toBeNull();
    expect($game->fresh()->club2_score)->toBeNull();
});
