<?php

use App\Models\Club;
use App\Models\User;
use App\Permission;
use Illuminate\Support\Facades\Gate;
use Inertia\Testing\AssertableInertia as Assert;

test('clubs can be listed, viewed, created, updated and deleted', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    Gate::before(fn (User $candidate) => $candidate->is($user) ? true : null);
    $club = Club::factory()->create();
    $this->get(route('clubs.index'))->assertOk()->assertInertia(fn (Assert $page) => $page->component('clubs/Index')->has('records', 1));
    $this->get(route('clubs.create'))->assertOk()->assertInertia(fn (Assert $page) => $page->component('clubs/Form'));
    $this->get(route('clubs.show', $club))->assertOk()->assertInertia(fn (Assert $page) => $page->component('clubs/Show')->where('record.id', $club->id));
    $this->get(route('clubs.edit', $club))->assertOk()->assertInertia(fn (Assert $page) => $page->component('clubs/Form')->where('record.id', $club->id));
    $this->post(route('clubs.store'), ['name' => 'Town', 'description' => 'Local club'])->assertRedirect();
    $created = Club::query()->where('name', 'Town')->firstOrFail();
    $this->put(route('clubs.update', $created), ['name' => 'City', 'description' => 'New description'])->assertRedirect(route('clubs.show', $created->refresh()));
    expect($created->fresh()->name)->toBe('City');
    expect($created->fresh()->description)->toBe('New description');
    $this->delete(route('clubs.destroy', $created))->assertRedirect(route('clubs.index'));
    $this->assertSoftDeleted($created);
});

test('club details must be valid', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    Gate::before(fn (User $candidate) => $candidate->is($user) ? true : null);
    $this->post(route('clubs.store'), [])->assertSessionHasErrors(['name', 'description']);
    $this->post(route('clubs.store'), ['name' => str_repeat('a', 256), 'description' => 'Club'])->assertSessionHasErrors(['name']);
    expect(Club::query()->count())->toBe(0);
});

test('club routes enforce the policy for guests and users without permissions', function () {
    $club = Club::factory()->create();
    $payload = ['name' => 'Changed', 'description' => 'Changed'];

    $this->get(route('clubs.show', $club))->assertForbidden();
    $this->get(route('clubs.index'))->assertForbidden();
    $this->post(route('clubs.store'), $payload)->assertForbidden();

    $this->actingAs(User::factory()->create());

    $this->get(route('clubs.index'))->assertOk();
    $this->get(route('clubs.create'))->assertOk();
    $this->get(route('clubs.show', $club))->assertForbidden();
    $this->get(route('clubs.edit', $club))->assertForbidden();
    $this->put(route('clubs.update', $club), $payload)->assertForbidden();
    $this->delete(route('clubs.destroy', $club))->assertForbidden();

    expect($club->fresh()->name)->toBe($club->name);
    $this->assertNotSoftDeleted($club);
    expect(Club::query()->count())->toBe(1);
});

test('authenticated users can create a club', function () {
    $this->actingAs(User::factory()->create());

    $this->post(route('clubs.store'), ['name' => 'Town', 'description' => 'Local club'])->assertRedirect();

    $this->assertDatabaseHas('clubs', ['name' => 'Town', 'description' => 'Local club']);
});

test('club members can use actions permitted on their club', function () {
    $user = User::factory()->create();
    $club = Club::factory()->create();
    $otherClub = Club::factory()->create();
    $user->clubs()->attach($club, ['permissions' => [Permission::View->value, Permission::Update->value, Permission::Delete->value]]);
    $this->actingAs($user);

    $this->get(route('clubs.show', $club))->assertOk();
    $this->get(route('clubs.edit', $club))->assertOk();
    $this->put(route('clubs.update', $club), ['name' => 'Updated Club', 'description' => 'Updated description'])->assertRedirect();
    $this->get(route('clubs.show', $otherClub))->assertForbidden();
    expect($club->fresh()->name)->toBe('Updated Club');
    $this->delete(route('clubs.destroy', $club->refresh()))->assertRedirect(route('clubs.index'));
    $this->assertSoftDeleted($club);
});
