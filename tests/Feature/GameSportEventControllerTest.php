<?php

use App\Events\GameEventRecorded;
use App\Models\Game;
use App\Models\GameUser;
use App\Models\Position;
use App\Models\SportEvent;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Inertia\Testing\AssertableInertia as Assert;

test('a game can record events for its teams and lineup players', function () {
    $this->actingAs(User::factory()->create(['superadmin' => true]));
    $this->withoutVite();
    $game = Game::factory()->create();
    $player = User::factory()->create();
    $position = Position::factory()->create(['sport_id' => $game->sport_id]);
    $game->users()->attach($player, ['team' => 1, 'position_id' => $position->id]);
    $gameUser = $game->users()->firstOrFail()->pivot;
    $event = SportEvent::factory()->create(['sport_id' => $game->sport_id]);
    $data = ['sport_event_id' => $event->id, 'occurred_at' => '2026-11-09T12:30'];
    Event::fake([GameEventRecorded::class]);

    $this->post(route('games.events.store', $game), [...$data, 'competitor_type' => 'player', 'competitor_id' => $gameUser->id, 'description' => 'A decisive play'])->assertRedirect(route('games.show', $game));
    $this->post(route('games.events.store', $game), [...$data, 'competitor_type' => 'team', 'competitor_id' => $game->team1_id])->assertRedirect(route('games.show', $game));

    $this->assertDatabaseHas('game_sport_event', ['game_id' => $game->id, 'sport_event_id' => $event->id, 'competitor_type' => GameUser::class, 'competitor_id' => $gameUser->id, 'description' => 'A decisive play']);
    $this->assertDatabaseHas('game_sport_event', ['game_id' => $game->id, 'sport_event_id' => $event->id, 'competitor_type' => Team::class, 'competitor_id' => $game->team1_id]);
    Event::assertDispatched(GameEventRecorded::class, 2);
    Event::assertDispatched(fn (GameEventRecorded $recorded) => $recorded->gameId === $game->id
        && $recorded->broadcastOn()[0]->name === 'games.'.$game->id
        && $recorded->broadcastWith() === []);
    $this->get(route('games.show', $game))->assertInertia(fn (Assert $page) => $page->has('events', 2)->where('events.0.description', 'A decisive play')->has('sportEvents', 1));
});

test('game events reject unrelated events and competitors and invalid times', function () {
    $this->actingAs(User::factory()->create(['superadmin' => true]));
    $game = Game::factory()->create();
    $otherGame = Game::factory()->create();
    $otherEvent = SportEvent::factory()->create();
    $validEvent = SportEvent::factory()->create(['sport_id' => $game->sport_id]);
    Event::fake([GameEventRecorded::class]);
    $data = ['sport_event_id' => $otherEvent->id, 'competitor_type' => 'team', 'competitor_id' => $otherGame->team1_id, 'occurred_at' => 'invalid'];

    $this->post(route('games.events.store', $game), $data)->assertSessionHasErrors(['sport_event_id', 'competitor_id', 'occurred_at']);
    $this->post(route('games.events.store', $game), [...$data, 'sport_event_id' => $validEvent->id, 'competitor_type' => 'player', 'competitor_id' => $game->team1_id, 'occurred_at' => '2026-11-09T12:30'])->assertSessionHasErrors('competitor_id');
    $this->post(route('games.events.store', $game), [...$data, 'sport_event_id' => $validEvent->id, 'competitor_type' => 'invalid', 'competitor_id' => $game->team1_id, 'occurred_at' => '2026-11-09T12:30'])->assertSessionHasErrors('competitor_type');
    $this->post(route('games.events.store', $game), [...$data, 'sport_event_id' => $validEvent->id, 'competitor_id' => $game->team1_id, 'occurred_at' => '2026-11-09T12:30', 'description' => ['invalid']])->assertSessionHasErrors('description');
    $this->assertDatabaseCount('game_sport_event', 0);
    Event::assertNotDispatched(GameEventRecorded::class);
});

test('game scores sum repeated team and player events for each side', function () {
    $this->withoutVite();
    $game = Game::factory()->create();
    $players = User::factory()->count(2)->create();
    $position = Position::factory()->create(['sport_id' => $game->sport_id]);
    $game->users()->attach($players[0], ['team' => 1, 'position_id' => $position->id]);
    $game->users()->attach($players[1], ['team' => 2, 'position_id' => $position->id]);
    $lineup = $game->users()->get()->keyBy('id');
    $try = SportEvent::factory()->create(['sport_id' => $game->sport_id, 'score' => 5]);
    $conversion = SportEvent::factory()->create(['sport_id' => $game->sport_id, 'score' => 2]);
    $penalty = SportEvent::factory()->create(['sport_id' => $game->sport_id, 'score' => 0]);
    $attributes = ['occurred_at' => '2026-11-09 12:30:00'];

    $this->get(route('games.show', $game))->assertInertia(fn (Assert $page) => $page->where('score.team1', 0)->where('score.team2', 0));
    $game->sportEvents()->attach($try, [...$attributes, 'competitor_type' => GameUser::class, 'competitor_id' => $lineup[$players[0]->id]->pivot->id]);
    $game->sportEvents()->attach($try, [...$attributes, 'competitor_type' => GameUser::class, 'competitor_id' => $lineup[$players[0]->id]->pivot->id]);
    $game->sportEvents()->attach($conversion, [...$attributes, 'competitor_type' => Team::class, 'competitor_id' => $game->team1_id]);
    $game->sportEvents()->attach($try, [...$attributes, 'competitor_type' => GameUser::class, 'competitor_id' => $lineup[$players[1]->id]->pivot->id]);
    $game->sportEvents()->attach($conversion, [...$attributes, 'competitor_type' => Team::class, 'competitor_id' => $game->team2_id]);
    $game->sportEvents()->attach($penalty, [...$attributes, 'competitor_type' => Team::class, 'competitor_id' => $game->team2_id]);

    $this->get(route('games.show', $game))->assertInertia(fn (Assert $page) => $page->where('score.team1', 12)->where('score.team2', 7));
});
