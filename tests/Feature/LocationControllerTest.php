<?php

use App\Models\Location;
use Inertia\Testing\AssertableInertia as Assert;

test('locations can be listed, viewed, created, updated and deleted', function () {
    $location = Location::factory()->create();
    $this->get(route('locations.index'))->assertOk()->assertInertia(fn (Assert $page) => $page->component('locations/Index')->has('records', 1));
    $this->get(route('locations.create'))->assertOk()->assertInertia(fn (Assert $page) => $page->component('locations/Form'));
    $this->get(route('locations.show', $location))->assertOk()->assertInertia(fn (Assert $page) => $page->component('locations/Show')->where('record.id', $location->id));
    $this->get(route('locations.edit', $location))->assertOk()->assertInertia(fn (Assert $page) => $page->component('locations/Form')->where('record.id', $location->id));

    $this->post(route('locations.store'), ['name' => 'Ground', 'description' => 'A field', 'additional_information' => '{"parking":true}'])->assertRedirect();
    $created = Location::query()->where('name', 'Ground')->firstOrFail();
    expect($created->additional_information)->toBe(['parking' => true]);
    $this->put(route('locations.update', $created), ['name' => 'New Ground', 'description' => 'A pitch', 'additional_information' => '{"parking":false}'])->assertRedirect(route('locations.show', $created));
    expect($created->fresh()->additional_information)->toBe(['parking' => false]);
    expect($created->fresh()->name)->toBe('New Ground');
    $this->delete(route('locations.destroy', $created))->assertRedirect(route('locations.index'));
    $this->assertSoftDeleted($created);
});

test('location details must be valid', function () {
    $this->post(route('locations.store'), [])->assertSessionHasErrors(['name', 'description', 'additional_information']);
    $this->post(route('locations.store'), ['name' => str_repeat('a', 256), 'description' => 'A field', 'additional_information' => 'invalid'])->assertSessionHasErrors(['name', 'additional_information']);
    $this->post(route('locations.store'), ['name' => 'Ground', 'description' => 'A field', 'additional_information' => 'null'])->assertSessionHasErrors(['additional_information']);
    expect(Location::query()->count())->toBe(0);
});
