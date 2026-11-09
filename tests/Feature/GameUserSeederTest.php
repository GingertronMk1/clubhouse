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

test('seeded players on each side have distinct positions from the game sport', function () {
    $game = Game::factory()->create();
    $firstPlayers = User::factory()->count(8)->create();
    $secondPlayers = User::factory()->count(8)->create();
    $game->team1->club->users()->attach($firstPlayers->mapWithKeys(fn (User $user) => [$user->id => ['permissions' => '[]']])->all());
    $game->team2->club->users()->attach($secondPlayers->mapWithKeys(fn (User $user) => [$user->id => ['permissions' => '[]']])->all());
    $positions = Position::factory()->count(3)->create(['sport_id' => $game->sport_id]);
    Position::factory()->create();
    Game::factory()->count(20)->create(['sport_id' => $game->sport_id, 'competition_id' => $game->competition_id, 'team1_id' => $game->team1_id, 'team2_id' => $game->team2_id, 'location_id' => $game->location_id]);

    $this->seed(GameUserSeeder::class);

    foreach (Game::query()->with(['team1Users', 'team2Users'])->get() as $seededGame) {
        foreach ([$seededGame->team1Users, $seededGame->team2Users] as $lineup) {
            $positionIds = $lineup->pluck('pivot.position_id');
            expect($positionIds)->toHaveCount($positionIds->unique()->count());
            expect($positionIds->diff($positions->modelKeys()))->toBeEmpty();
        }
    }
});

test('lineup assignments follow stable player and position order', function () {
    $game = Game::factory()->create();
    $firstPlayer = User::factory()->create(['email' => 'a@example.com']);
    $secondPlayer = User::factory()->create(['email' => 'b@example.com']);
    $extraPlayer = User::factory()->create(['email' => 'c@example.com']);
    $game->team1->club->users()->attach([$extraPlayer->id => ['permissions' => '[]'], $secondPlayer->id => ['permissions' => '[]'], $firstPlayer->id => ['permissions' => '[]']]);
    $laterPosition = Position::factory()->create(['sport_id' => $game->sport_id, 'sort_order' => 2]);
    $firstPosition = Position::factory()->create(['sport_id' => $game->sport_id, 'sort_order' => 1]);

    $this->seed(GameUserSeeder::class);

    $this->assertDatabaseHas('game_user', ['game_id' => $game->id, 'user_id' => $firstPlayer->id, 'team' => 1, 'position_id' => $firstPosition->id]);
    $this->assertDatabaseHas('game_user', ['game_id' => $game->id, 'user_id' => $secondPlayer->id, 'team' => 1, 'position_id' => $laterPosition->id]);
    $this->assertDatabaseMissing('game_user', ['game_id' => $game->id, 'user_id' => $extraPlayer->id]);
});
