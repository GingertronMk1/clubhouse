<?php

use App\Models\Competition;
use App\Models\Location;
use Inertia\Testing\AssertableInertia as Assert;

test('competitions can be created viewed updated and deleted', function () {
    $this->withoutVite();
    $location = Location::factory()->create();
    $parent = Competition::factory()->create();
    $data = ['name' => 'Cup', 'description' => 'Annual cup', 'location_id' => $location->id, 'parent_id' => $parent->id];

    $this->get(route('competitions.create'))->assertInertia(fn (Assert $page) => $page->component('competitions/Form')->has('locations')->has('competitions'));
    $this->post(route('competitions.store'), $data)->assertRedirect();
    $competition = Competition::query()->where('name', 'Cup')->firstOrFail();
    $this->get(route('competitions.index'))->assertInertia(fn (Assert $page) => $page->component('competitions/Index')->has('records', 2));
    $this->get(route('competitions.show', $competition))->assertInertia(fn (Assert $page) => $page->component('competitions/Show')->where('record.parent.id', $parent->id));
    $this->get(route('competitions.edit', $competition))->assertInertia(fn (Assert $page) => $page->component('competitions/Form')->where('record.id', $competition->id));
    $this->put(route('competitions.update', $competition), [...$data, 'name' => 'League', 'parent_id' => null])->assertRedirect();
    expect($competition->refresh()->name)->toBe('League')->and($competition->parent_id)->toBeNull();
    $this->delete(route('competitions.destroy', $competition))->assertRedirect(route('competitions.index'));
    $this->assertSoftDeleted($competition);
});

test('competitions reject invalid locations and cycles in their hierarchy', function () {
    $parent = Competition::factory()->create();
    $child = Competition::factory()->for($parent, 'parent')->create();
    $data = ['name' => 'Cup', 'description' => 'Cup', 'location_id' => $parent->location_id, 'parent_id' => $child->id];

    $this->put(route('competitions.update', $parent), $data)->assertSessionHasErrors('parent_id');
    $this->post(route('competitions.store'), [...$data, 'location_id' => 'invalid'])->assertSessionHasErrors('location_id');
    expect($parent->refresh()->parent_id)->toBeNull();
});
