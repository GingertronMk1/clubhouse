<?php

use App\Models\Game;
use App\Models\Position;
use App\Models\Team;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('games can be created viewed updated and deleted', function () {
    $this->withoutVite();
    $sample = Game::factory()->create();
    $data = ['name' => 'Final', 'start' => '2026-11-08T12:30', 'description' => 'Final match', 'summary' => 'A good game', 'sport_id' => $sample->sport_id, 'competition_id' => $sample->competition_id, 'team1_id' => $sample->team1_id, 'team2_id' => $sample->team2_id, 'location_id' => $sample->location_id, 'score' => ['home' => 2, 'away' => 1]];

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
    $data = ['name' => 'Final', 'start' => '2026-11-08T12:30', 'description' => 'Match', 'summary' => 'Summary', 'sport_id' => $sample->sport_id, 'competition_id' => $sample->competition_id, 'team1_id' => $sample->team1_id, 'team2_id' => $sample->team1_id, 'location_id' => $sample->location_id];

    $this->post(route('games.store'), $data)->assertSessionHasErrors('team2_id');
    $this->post(route('games.store'), [...$data, 'team2_id' => $team->id])->assertSessionHasErrors('team2_id');
    expect(Game::query()->count())->toBe(1);
});

test('lineups can be saved and replaced when editing a game', function () {
    $this->withoutVite();
    $game = Game::factory()->create();
    $player = User::factory()->create();
    $game->team1->club->users()->attach($player, ['permissions' => '[]']);
    $position = Position::factory()->create(['sport_id' => $game->sport_id]);
    $data = ['name' => 'Final', 'description' => 'Match', 'summary' => 'Summary', 'sport_id' => $game->sport_id, 'competition_id' => $game->competition_id, 'team1_id' => $game->team1_id, 'team2_id' => $game->team2_id, 'location_id' => $game->location_id, 'lineup' => [['user_id' => $player->id, 'team' => 1, 'position_id' => $position->id, 'jersey_number' => 7]]];

    $this->put(route('games.update', $game), $data)->assertRedirect(route('games.show', $game));
    $this->assertDatabaseHas('game_user', ['game_id' => $game->id, 'user_id' => $player->id, 'team' => 1, 'position_id' => $position->id, 'jersey_number' => 7]);
    $this->get(route('games.edit', $game))->assertInertia(fn (Assert $page) => $page->where('record.users.0.id', $player->id)->where('record.users.0.pivot.team', 1));
    expect($player->games()->first()->pivot->position->id)->toBe($position->id);

    $this->put(route('games.update', $game), [...$data, 'lineup' => []])->assertRedirect();
    $this->assertDatabaseMissing('game_user', ['game_id' => $game->id, 'user_id' => $player->id]);
});

test('lineups reject players outside the team club and positions outside the sport', function () {
    $game = Game::factory()->create();
    $player = User::factory()->create();
    $position = Position::factory()->create();
    $data = ['name' => 'Final', 'description' => 'Match', 'summary' => 'Summary', 'sport_id' => $game->sport_id, 'competition_id' => $game->competition_id, 'team1_id' => $game->team1_id, 'team2_id' => $game->team2_id, 'location_id' => $game->location_id, 'lineup' => [['user_id' => $player->id, 'team' => 1, 'position_id' => $position->id, 'jersey_number' => null]]];

    $this->post(route('games.store'), $data)->assertSessionHasErrors(['lineup.0.user_id', 'lineup.0.position_id']);
    $this->assertDatabaseCount('game_user', 0);
});

test('a game can be created with a lineup', function () {
    $sample = Game::factory()->create();
    $player = User::factory()->create();
    $sample->team2->club->users()->attach($player, ['permissions' => '[]']);
    $position = Position::factory()->create(['sport_id' => $sample->sport_id]);
    $data = ['name' => 'Lineup game', 'description' => 'Match', 'summary' => 'Summary', 'sport_id' => $sample->sport_id, 'competition_id' => $sample->competition_id, 'team1_id' => $sample->team1_id, 'team2_id' => $sample->team2_id, 'location_id' => $sample->location_id, 'lineup' => [['user_id' => $player->id, 'team' => 2, 'position_id' => $position->id, 'jersey_number' => null]]];

    $this->post(route('games.store'), $data)->assertRedirect();
    $game = Game::query()->where('name', 'Lineup game')->firstOrFail();
    $this->assertDatabaseHas('game_user', ['game_id' => $game->id, 'user_id' => $player->id, 'team' => 2, 'position_id' => $position->id, 'jersey_number' => null]);
});

test('lineups reject invalid sides and duplicate players', function () {
    $game = Game::factory()->create();
    $otherTeam = Team::factory()->create();
    $player = User::factory()->create();
    $otherTeam->club->users()->attach($player, ['permissions' => '[]']);
    $position = Position::factory()->create(['sport_id' => $game->sport_id]);
    $entry = ['user_id' => $player->id, 'team' => 3, 'position_id' => $position->id];
    $data = ['name' => 'Final', 'description' => 'Match', 'summary' => 'Summary', 'sport_id' => $game->sport_id, 'competition_id' => $game->competition_id, 'team1_id' => $game->team1_id, 'team2_id' => $game->team2_id, 'location_id' => $game->location_id, 'lineup' => [$entry, $entry]];

    $this->put(route('games.update', $game), $data)->assertSessionHasErrors(['lineup.0.team', 'lineup.0.user_id']);
    $this->assertDatabaseCount('game_user', 0);
});
