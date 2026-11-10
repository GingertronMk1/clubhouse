<?php

use App\Models\Club;
use App\Models\Game;
use App\Models\Sport;
use App\Models\SportEvent;
use App\Models\Team;
use App\Models\User;
use App\Permission;

test('sport and sport event changes are restricted to superadmins', function () {
    $sport = Sport::factory()->create();
    $user = User::factory()->create();
    $data = ['name' => 'Rugby', 'description' => 'Description', 'events' => [['name' => 'Try', 'description' => '', 'score' => 5]]];

    $this->get(route('sports.create'))->assertForbidden();
    $this->post(route('sports.store'), $data)->assertForbidden();
    $this->actingAs($user)->get(route('sports.edit', $sport))->assertForbidden();
    $this->put(route('sports.update', $sport), $data)->assertForbidden();
    $this->delete(route('sports.destroy', $sport))->assertForbidden();
    expect($sport->fresh()->name)->not->toBe('Rugby');

    $admin = User::factory()->create(['superadmin' => true]);
    $this->actingAs($admin)->get(route('sports.create'))->assertOk();
    $this->get(route('sports.edit', $sport))->assertOk();
    $this->actingAs($admin)->put(route('sports.update', $sport), $data)->assertRedirect(route('sports.index'));
    expect($sport->fresh()->events()->where('name', 'Try')->exists())->toBeTrue();
    $this->post(route('sports.store'), ['name' => 'Another sport', 'description' => 'Description', 'events' => []])->assertRedirect();
    $this->delete(route('sports.destroy', $sport->fresh()))->assertRedirect();
});

test('team viewing is public and modifications require the matching club permission', function () {
    $club = Club::factory()->create();
    $team = Team::factory()->for($club)->create();
    $otherClub = Club::factory()->create();
    $user = User::factory()->create();
    $data = ['name' => 'Changed', 'description' => 'Description'];

    $this->get(route('clubs.teams.index', $club))->assertOk();
    $this->get(route('clubs.teams.show', [$club, $team]))->assertOk();
    $this->post(route('clubs.teams.store', $club), $data)->assertForbidden();
    $user->clubs()->attach($otherClub, ['permissions' => [Permission::Create->value, Permission::Update->value, Permission::Delete->value]]);
    $this->actingAs($user)->post(route('clubs.teams.store', $club), $data)->assertForbidden();
    $user->clubs()->attach($club, ['permissions' => [Permission::Create->value]]);
    $this->get(route('clubs.teams.create', $club))->assertOk();
    $this->post(route('clubs.teams.store', $club), $data)->assertRedirect();
    $this->get(route('clubs.teams.edit', [$club, $team]))->assertForbidden();
    $this->put(route('clubs.teams.update', [$club, $team]), $data)->assertForbidden();
    $this->delete(route('clubs.teams.destroy', [$club, $team]))->assertForbidden();
    $user->clubs()->updateExistingPivot($club, ['permissions' => [Permission::Update->value, Permission::Delete->value]]);
    $this->put(route('clubs.teams.update', [$club, $team]), $data)->assertRedirect();
    $this->delete(route('clubs.teams.destroy', [$club, $team->fresh()]))->assertRedirect();
    $this->assertSoftDeleted($team);
});

test('either participating club can record game events with create permission', function () {
    $game = Game::factory()->create();
    $event = SportEvent::factory()->create(['sport_id' => $game->sport_id]);
    $data = ['sport_event_id' => $event->id, 'competitor_type' => 'team', 'competitor_id' => $game->team1_id, 'occurred_at' => '2026-11-09T12:30'];
    $user = User::factory()->create();

    $this->post(route('games.events.store', $game), $data)->assertForbidden();
    $this->actingAs($user)->post(route('games.events.store', $game), $data)->assertForbidden();
    $user->clubs()->attach($game->team1->club, ['permissions' => [Permission::Update->value]]);
    $this->post(route('games.events.store', $game), $data)->assertForbidden();
    $user->clubs()->attach($game->team2->club, ['permissions' => [Permission::Create->value]]);
    $this->post(route('games.events.store', $game), $data)->assertRedirect(route('games.show', $game));
    $user->clubs()->updateExistingPivot($game->team1->club, ['permissions' => [Permission::Create->value]]);
    $user->clubs()->updateExistingPivot($game->team2->club, ['permissions' => []]);
    $this->post(route('games.events.store', $game), $data)->assertRedirect(route('games.show', $game));
    $this->assertDatabaseCount('game_sport_event', 2);
});

test('superadmins can modify teams and record events without club membership', function () {
    $admin = User::factory()->create(['superadmin' => true]);
    $game = Game::factory()->create();
    $event = SportEvent::factory()->create(['sport_id' => $game->sport_id]);
    $club = $game->team1->club;

    $this->actingAs($admin)->post(route('clubs.teams.store', $club), ['name' => 'New team', 'description' => 'Description'])->assertRedirect();
    $team = $club->teams()->where('name', 'New team')->firstOrFail();
    $this->get(route('clubs.teams.edit', [$club, $team]))->assertOk();
    $this->put(route('clubs.teams.update', [$club, $team]), ['name' => 'Renamed team', 'description' => 'Description'])->assertRedirect();
    $this->delete(route('clubs.teams.destroy', [$club, $team->fresh()]))->assertRedirect();
    $this->post(route('games.events.store', $game), ['sport_event_id' => $event->id, 'competitor_type' => 'team', 'competitor_id' => $game->team1_id, 'occurred_at' => '2026-11-09T12:30'])->assertRedirect();
    $this->assertDatabaseCount('game_sport_event', 1);
});
