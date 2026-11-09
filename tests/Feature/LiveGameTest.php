<?php

use App\Models\Game;
use Inertia\Testing\AssertableInertia as Assert;

test('the live game page is accessible', function () {
    $game = Game::factory()->create();

    $this->get(route('live-game', $game))->assertOk()->assertInertia(fn (Assert $page) => $page
        ->component('LiveGame')
        ->where('game.id', $game->id)
        ->where('game.name', $game->name));
});

test('the live game page returns not found for a deleted game', function () {
    $game = Game::factory()->create();
    $game->delete();

    $this->get(route('live-game', $game))->assertNotFound();
});
