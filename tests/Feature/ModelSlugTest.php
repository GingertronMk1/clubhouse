<?php

use App\Models\Club;
use App\Models\Sport;
use App\Models\Team;

it('generates a slug from the name and updates it when the name changes', function (string $modelClass) {
    $model = $modelClass::factory()->create(['name' => 'Rugby Union']);

    expect($model->refresh()->slug)->toBe('rugby-union');

    $model->update(['name' => 'Rugby League']);

    expect($model->refresh()->slug)->toBe('rugby-league');

    $model->update(['description' => 'Updated description']);

    expect($model->refresh()->slug)->toBe('rugby-league');
})->with([Sport::class, Club::class, Team::class]);

it('keeps slugs unique including for deleted records and renames', function (string $modelClass) {
    $first = $modelClass::factory()->create(['name' => 'Rugby Union']);
    $second = $modelClass::factory()->create(['name' => 'Rugby Union']);
    $first->delete();
    $third = $modelClass::factory()->create(['name' => 'Rugby Union']);

    expect($first->slug)->toBe('rugby-union')
        ->and($second->slug)->toBe('rugby-union-2')
        ->and($third->slug)->toBe('rugby-union-3');

    $third->update(['name' => 'Rugby Union!']);

    expect($third->refresh()->slug)->toBe('rugby-union-3');
})->with([Sport::class, Club::class]);

it('allows teams to share a slug', function () {
    $first = Team::factory()->create(['name' => 'First Team']);
    $second = Team::factory()->create(['name' => 'First Team']);

    expect($first->slug)->toBe('first-team')
        ->and($second->slug)->toBe('first-team');
});

it('keeps team slugs unique within each club', function () {
    $club = Club::factory()->create();
    $first = Team::factory()->for($club)->create(['name' => 'First Team']);
    $second = Team::factory()->for($club)->create(['name' => 'First Team']);

    expect($first->slug)->toBe('first-team')
        ->and($second->slug)->toBe('first-team-2');
});

it('binds slug routes to the correct records', function () {
    $this->withoutVite();
    $sport = Sport::factory()->create(['name' => 'Rugby Union']);
    $club = Club::factory()->create(['name' => 'Town Club']);
    $otherClub = Club::factory()->create(['name' => 'City Club']);
    $team = Team::factory()->for($club)->create(['name' => 'First Team']);
    $otherTeam = Team::factory()->for($otherClub)->create(['name' => 'First Team']);
    $secondTeam = Team::factory()->for($club)->create(['name' => 'First Team']);

    expect(route('sports.show', $sport))->toEndWith('/sports/rugby-union')
        ->and(route('clubs.show', $club))->toEndWith('/clubs/town-club')
        ->and(route('clubs.teams.show', [$club, $team]))->toEndWith('/clubs/town-club/teams/first-team');

    $this->get(route('sports.show', $sport))->assertOk();
    $this->get(route('clubs.show', $club))->assertOk();
    $this->get(route('clubs.teams.show', [$club, $team]))->assertInertia(fn ($page) => $page->where('team.id', $team->id));
    $this->get(route('clubs.teams.show', [$otherClub, $otherTeam]))->assertInertia(fn ($page) => $page->where('team.id', $otherTeam->id));
    $this->get(route('clubs.teams.show', [$club, $secondTeam]))->assertInertia(fn ($page) => $page->where('team.id', $secondTeam->id));
    $this->get('/clubs/city-club/teams/first-team-2')->assertNotFound();
    $this->get('/sports/'.$sport->id)->assertNotFound();
});
