<?php

use App\Models\Game;
use App\Models\Position;
use App\Models\SportEvent;
use App\Models\User;

test('game events retain their competing player or team and can occur more than once', function () {
    $game = Game::factory()->create();
    $player = User::factory()->create();
    $position = Position::factory()->create(['sport_id' => $game->sport_id]);
    $game->users()->attach($player, ['team' => 1, 'position_id' => $position->id]);
    $gameUser = $game->users()->firstOrFail()->pivot;
    $event = SportEvent::factory()->create(['sport_id' => $game->sport_id]);

    $game->sportEvents()->attach($event, ['competitor_type' => $gameUser->getMorphClass(), 'competitor_id' => $gameUser->id, 'occurred_at' => '2026-11-09 12:30:00']);
    $game->sportEvents()->attach($event, ['competitor_type' => $game->team1->getMorphClass(), 'competitor_id' => $game->team1->id, 'occurred_at' => '2026-11-09 12:45:00']);

    $events = $game->sportEvents()->get();
    expect($events)->toHaveCount(2);
    expect($events[0]->pivot->competitor->is($gameUser))->toBeTrue();
    expect($events[1]->pivot->competitor->is($game->team1))->toBeTrue();
    expect($events[0]->pivot->occurred_at->format('Y-m-d H:i:s'))->toBe('2026-11-09 12:30:00');
    expect($event->games()->firstOrFail()->pivot->occurred_at->format('Y-m-d H:i:s'))->toBe('2026-11-09 12:30:00');
    expect($event->games()->count())->toBe(2);
});
