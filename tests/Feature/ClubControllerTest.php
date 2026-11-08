<?php

use App\Models\Club;
use Inertia\Testing\AssertableInertia as Assert;

test('clubs can be listed, viewed, created, updated and deleted', function () {
    $club = Club::factory()->create();
    $this->get(route('clubs.index'))->assertOk()->assertInertia(fn (Assert $page) => $page->component('directory/Index')->has('records', 1));
    $this->get(route('clubs.create'))->assertOk()->assertInertia(fn (Assert $page) => $page->component('directory/Form'));
    $this->get(route('clubs.show', $club))->assertOk()->assertInertia(fn (Assert $page) => $page->component('directory/Show')->where('record.id', $club->id));
    $this->get(route('clubs.edit', $club))->assertOk()->assertInertia(fn (Assert $page) => $page->component('directory/Form')->where('record.id', $club->id));
    $this->post(route('clubs.store'), ['name' => 'Town', 'description' => 'Local club'])->assertRedirect();
    $created = Club::where('name', 'Town')->firstOrFail();
    $this->put(route('clubs.update', $created), ['name' => 'City', 'description' => 'New description'])->assertRedirect(route('clubs.show', $created));
    expect($created->fresh()->name)->toBe('City');
    expect($created->fresh()->description)->toBe('New description');
    $this->delete(route('clubs.destroy', $created))->assertRedirect(route('clubs.index'));
    $this->assertSoftDeleted($created);
});

test('club details must be valid', function () {
    $this->post(route('clubs.store'), [])->assertSessionHasErrors(['name', 'description']);
    $this->post(route('clubs.store'), ['name' => str_repeat('a', 256), 'description' => 'Club'])->assertSessionHasErrors(['name']);
    expect(Club::count())->toBe(0);
});
