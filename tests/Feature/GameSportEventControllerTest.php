<?php

use App\Models\Game;
use App\Models\GameUser;
use App\Models\Position;
use App\Models\SportEvent;
use App\Models\Team;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('a game can record events for its teams and lineup players', function () {
    $this->withoutVite();
    $game = Game::factory()->create();
    $player = User::factory()->create();
    $position = Position::factory()->create(['sport_id' => $game->sport_id]);
    $game->users()->attach($player, ['team' => 1, 'position_id' => $position->id]);
    $gameUser = $game->users()->firstOrFail()->pivot;
    $event = SportEvent::factory()->create(['sport_id' => $game->sport_id]);
    $data = ['sport_event_id' => $event->id, 'occurred_at' => '2026-11-09T12:30'];

    $this->post(route('games.events.store', $game), [...$data, 'competitor_type' => 'player', 'competitor_id' => $gameUser->id, 'description' => 'A decisive play'])->assertRedirect(route('games.show', $game));
    $this->post(route('games.events.store', $game), [...$data, 'competitor_type' => 'team', 'competitor_id' => $game->team1_id])->assertRedirect(route('games.show', $game));

    $this->assertDatabaseHas('game_sport_event', ['game_id' => $game->id, 'sport_event_id' => $event->id, 'competitor_type' => GameUser::class, 'competitor_id' => $gameUser->id, 'description' => 'A decisive play']);
    $this->assertDatabaseHas('game_sport_event', ['game_id' => $game->id, 'sport_event_id' => $event->id, 'competitor_type' => Team::class, 'competitor_id' => $game->team1_id]);
    $this->get(route('games.show', $game))->assertInertia(fn (Assert $page) => $page->has('events', 2)->where('events.0.description', 'A decisive play')->has('sportEvents', 1));
});

test('game events reject unrelated events and competitors and invalid times', function () {
    $game = Game::factory()->create();
    $otherGame = Game::factory()->create();
    $otherEvent = SportEvent::factory()->create();
    $validEvent = SportEvent::factory()->create(['sport_id' => $game->sport_id]);
    $data = ['sport_event_id' => $otherEvent->id, 'competitor_type' => 'team', 'competitor_id' => $otherGame->team1_id, 'occurred_at' => 'invalid'];

    $this->post(route('games.events.store', $game), $data)->assertSessionHasErrors(['sport_event_id', 'competitor_id', 'occurred_at']);
    $this->post(route('games.events.store', $game), [...$data, 'sport_event_id' => $validEvent->id, 'competitor_type' => 'player', 'competitor_id' => $game->team1_id, 'occurred_at' => '2026-11-09T12:30'])->assertSessionHasErrors('competitor_id');
    $this->post(route('games.events.store', $game), [...$data, 'sport_event_id' => $validEvent->id, 'competitor_type' => 'invalid', 'competitor_id' => $game->team1_id, 'occurred_at' => '2026-11-09T12:30'])->assertSessionHasErrors('competitor_type');
    $this->post(route('games.events.store', $game), [...$data, 'sport_event_id' => $validEvent->id, 'competitor_id' => $game->team1_id, 'occurred_at' => '2026-11-09T12:30', 'description' => ['invalid']])->assertSessionHasErrors('description');
    $this->assertDatabaseCount('game_sport_event', 0);
});
