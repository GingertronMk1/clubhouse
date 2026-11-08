<?php

use App\Models\Club;
use App\Models\Team;
use App\Models\User;
use App\Permission;
use Inertia\Testing\AssertableInertia as Assert;

test('teams can be created, listed, viewed, updated and deleted for a club', function () {
    $this->withoutVite();
    $club = Club::factory()->create();
    $user = User::factory()->create();
    $user->clubs()->attach($club, ['permissions' => [Permission::View->value]]);
    $this->actingAs($user);
    Team::factory()->for($club)->create(['name' => 'Zebras']);
    Team::factory()->create(['name' => 'Other club']);
    $data = ['name' => 'Athletics', 'description' => 'First team'];

    $this->get(route('clubs.teams.create', $club))->assertInertia(fn (Assert $page) => $page->component('teams/Form')->where('club.id', $club->id)->missing('team'));
    $this->post(route('clubs.teams.store', $club), $data)->assertRedirect(route('clubs.teams.index', $club));
    $team = $club->teams()->where('name', 'Athletics')->firstOrFail();
    expect($team->club->is($club))->toBeTrue();
    $this->get(route('clubs.show', $club))->assertInertia(fn (Assert $page) => $page->has('teams', 2)->where('teams.0.name', 'Athletics'));
    $this->get(route('clubs.teams.index', $club))->assertInertia(fn (Assert $page) => $page->component('teams/Index')->has('teams', 2)->where('teams.0.name', 'Athletics')->where('teams.1.name', 'Zebras'));
    $this->get(route('clubs.teams.show', [$club, $team]))->assertInertia(fn (Assert $page) => $page->component('teams/Show')->where('team.name', 'Athletics'));
    $this->get(route('clubs.teams.edit', [$club, $team]))->assertInertia(fn (Assert $page) => $page->component('teams/Form')->where('team.name', 'Athletics'));
    $this->put(route('clubs.teams.update', [$club, $team]), [...$data, 'name' => 'City'])->assertRedirect(route('clubs.teams.index', $club));
    expect($team->refresh()->name)->toBe('City');
    $this->delete(route('clubs.teams.destroy', [$club, $team]))->assertRedirect(route('clubs.teams.index', $club));
    $this->assertSoftDeleted($team);
});

test('teams require valid fields and cannot be assigned to another club', function () {
    $club = Club::factory()->create();
    $otherClub = Club::factory()->create();
    $this->post(route('clubs.teams.store', $club), [])->assertSessionHasErrors(['name', 'description']);
    $this->post(route('clubs.teams.store', $club), ['name' => str_repeat('a', 256), 'description' => 'Team'])->assertSessionHasErrors('name');
    $this->post(route('clubs.teams.store', $club), ['name' => 'Team', 'description' => 'Description', 'club_id' => $otherClub->id])->assertRedirect();
    expect($club->teams()->count())->toBe(1);
    expect($otherClub->teams()->count())->toBe(0);
});

test('teams cannot be accessed through a different club', function () {
    $club = Club::factory()->create();
    $team = Team::factory()->create();
    $this->get(route('clubs.teams.show', [$club, $team]))->assertNotFound();
    $this->get(route('clubs.teams.edit', [$club, $team]))->assertNotFound();
    $this->put(route('clubs.teams.update', [$club, $team]), [])->assertNotFound();
    $this->delete(route('clubs.teams.destroy', [$club, $team]))->assertNotFound();
    expect($team->refresh()->trashed())->toBeFalse();
});
