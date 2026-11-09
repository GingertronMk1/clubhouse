<?php

use App\Models\Game;
use App\Models\Position;
use App\Models\User;
use Database\Seeders\GameUserSeeder;

test('game lineups are seeded from each club and preserved on subsequent runs', function () {
    $game = Game::factory()->create();
    $firstPlayer = User::factory()->create();
    $secondPlayer = User::factory()->create();
    $game->team1->club->users()->attach($firstPlayer, ['permissions' => '[]']);
    $game->team2->club->users()->attach($secondPlayer, ['permissions' => '[]']);
    $position = Position::factory()->create(['sport_id' => $game->sport_id]);

    $this->seed(GameUserSeeder::class);
    $this->seed(GameUserSeeder::class);

    $this->assertDatabaseCount('game_user', 2);
    $this->assertDatabaseHas('game_user', ['game_id' => $game->id, 'user_id' => $firstPlayer->id, 'team' => 1, 'position_id' => $position->id]);
    $this->assertDatabaseHas('game_user', ['game_id' => $game->id, 'user_id' => $secondPlayer->id, 'team' => 2, 'position_id' => $position->id]);
});

test('shared club members are not selected for both teams', function () {
    $game = Game::factory()->create();
    $shared = User::factory()->create();
    $firstPlayer = User::factory()->create();
    $game->team1->club->users()->attach([$shared->id => ['permissions' => '[]'], $firstPlayer->id => ['permissions' => '[]']]);
    $game->team2->club->users()->attach($shared, ['permissions' => '[]']);
    Position::factory()->create(['sport_id' => $game->sport_id]);

    $this->seed(GameUserSeeder::class);

    $this->assertDatabaseCount('game_user', 2);
    $this->assertDatabaseHas('game_user', ['game_id' => $game->id, 'user_id' => $firstPlayer->id, 'team' => 1]);
    $this->assertDatabaseHas('game_user', ['game_id' => $game->id, 'user_id' => $shared->id, 'team' => 2]);
});

test('games without positions or club members are skipped', function () {
    $game = Game::factory()->create();
    $game->team1->club->users()->attach(User::factory()->create(), ['permissions' => '[]']);

    $this->seed(GameUserSeeder::class);

    $this->assertDatabaseCount('game_user', 0);
});

test('each seeded team has no more players than the sport has positions', function () {
    $game = Game::factory()->create();
    $firstPlayers = User::factory()->count(4)->create();
    $secondPlayers = User::factory()->count(4)->create();
    $game->team1->club->users()->attach($firstPlayers->mapWithKeys(fn (User $user) => [$user->id => ['permissions' => '[]']])->all());
    $game->team2->club->users()->attach($secondPlayers->mapWithKeys(fn (User $user) => [$user->id => ['permissions' => '[]']])->all());
    Position::factory()->create(['sport_id' => $game->sport_id]);
    Position::factory()->count(3)->create();

    $this->seed(GameUserSeeder::class);

    expect($game->team1Users()->count())->toBe(1);
    expect($game->team2Users()->count())->toBe(1);
});
