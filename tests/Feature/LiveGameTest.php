<?php

use App\Models\Game;
use App\Models\GameUser;
use App\Models\Position;
use App\Models\SportEvent;
use App\Models\Team;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('the live game page is accessible', function () {
    $game = Game::factory()->create();

    $this->get(route('live-game', $game))->assertOk()->assertInertia(fn (Assert $page) => $page
        ->component('LiveGame')
        ->where('game.id', $game->id)
        ->where('game.name', $game->name)
        ->where('game.team1.club.name', $game->team1->club->name)
        ->where('game.team2.club.name', $game->team2->club->name));
});

test('the live game page includes team and player events without exposing user details', function () {
    $game = Game::factory()->create();
    $player = User::factory()->create(['name' => 'Alex Smith']);
    $position = Position::factory()->create(['sport_id' => $game->sport_id]);
    $game->users()->attach($player, ['team' => 1, 'position_id' => $position->id]);
    $gameUser = $game->users()->firstOrFail()->pivot;
    $event = SportEvent::factory()->create(['sport_id' => $game->sport_id, 'name' => 'Try']);
    $game->sportEvents()->attach($event, ['competitor_type' => GameUser::class, 'competitor_id' => $gameUser->id, 'occurred_at' => '2026-11-09 12:30:00']);
    $game->sportEvents()->attach($event, ['competitor_type' => Team::class, 'competitor_id' => $game->team2_id, 'occurred_at' => '2026-11-09 12:45:00']);

    $this->get(route('live-game', $game))->assertOk()->assertInertia(fn (Assert $page) => $page
        ->where('game.team1_id', $game->team1_id)
        ->where('game.team2.name', $game->team2->name)
        ->where('game.users.0.name', 'Alex Smith')
        ->where('game.users.0.pivot.id', $gameUser->id)
        ->missing('game.users.0.email')
        ->has('game.sport_events', 2)
        ->where('game.sport_events.0.name', 'Try')
        ->where('game.sport_events.0.pivot.competitor_id', $gameUser->id)
        ->where('game.sport_events.1.pivot.competitor_id', $game->team2_id));
});

test('the live game page returns not found for a deleted game', function () {
    $game = Game::factory()->create();
    $game->delete();

    $this->get(route('live-game', $game))->assertNotFound();
});
