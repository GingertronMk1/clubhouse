<?php

use App\Models\Game;
use App\Models\Team;
use Inertia\Testing\AssertableInertia as Assert;

test('games can be created viewed updated and deleted', function () {
    $this->withoutVite();
    $sample = Game::factory()->create();
    $data = ['name' => 'Final', 'start' => '2026-11-08T12:30', 'description' => 'Final match', 'summary' => 'A good game', 'sport_id' => $sample->sport_id, 'competition_id' => $sample->competition_id, 'club1_id' => $sample->club1_id, 'club2_id' => $sample->club2_id, 'location_id' => $sample->location_id, 'score' => ['home' => 2, 'away' => 1]];

    $this->get(route('games.create'))->assertInertia(fn (Assert $page) => $page->component('games/Form')->has('teams', 2));
    $this->post(route('games.store'), $data)->assertRedirect();
    $game = Game::query()->where('name', 'Final')->firstOrFail();
    $this->get(route('games.index'))->assertInertia(fn (Assert $page) => $page->component('games/Index')->has('records', 2));
    $this->get(route('games.show', $game))->assertInertia(fn (Assert $page) => $page->component('games/Show')->where('record.score', []));
    $this->get(route('games.edit', $game))->assertInertia(fn (Assert $page) => $page->component('games/Form')->where('start', '2026-11-08T12:30:00'));
    $this->put(route('games.update', $game), [...$data, 'name' => 'Semi-final'])->assertRedirect();
    expect($game->refresh()->name)->toBe('Semi-final')->and($game->score)->toBe([]);
    $this->delete(route('games.destroy', $game))->assertRedirect(route('games.index'));
    $this->assertSoftDeleted($game);
});

test('games reject duplicate teams and deleted relations', function () {
    $sample = Game::factory()->create();
    $team = Team::factory()->create();
    $team->delete();
    $data = ['name' => 'Final', 'start' => '2026-11-08T12:30', 'description' => 'Match', 'summary' => 'Summary', 'sport_id' => $sample->sport_id, 'competition_id' => $sample->competition_id, 'club1_id' => $sample->club1_id, 'club2_id' => $sample->club1_id, 'location_id' => $sample->location_id];

    $this->post(route('games.store'), $data)->assertSessionHasErrors('club2_id');
    $this->post(route('games.store'), [...$data, 'club2_id' => $team->id])->assertSessionHasErrors('club2_id');
    expect(Game::query()->count())->toBe(1);
});
