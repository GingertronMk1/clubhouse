<?php

use App\Models\Position;
use App\Models\Sport;
use Inertia\Testing\AssertableInertia as Assert;

test('positions can be created, listed in order, updated and deleted for a sport', function () {
    $this->withoutVite();
    $sport = Sport::factory()->create();
    Position::factory()->for($sport)->create(['name' => 'Later', 'sort_order' => 2]);
    $data = ['name' => 'Prop', 'description' => 'A forward', 'preview_x' => 10, 'preview_y' => 20, 'sort_order' => 1, 'default_number' => 8];

    $this->get(route('sports.positions.create', $sport))->assertInertia(fn (Assert $page) => $page->component('positions/Form')->where('sport.id', $sport->id)->missing('position'));
    $this->post(route('sports.positions.store', $sport), $data)->assertRedirect(route('sports.positions.index', $sport));
    $position = $sport->positions()->where('name', 'Prop')->firstOrFail();
    expect($position->sport->is($sport))->toBeTrue();
    $this->get(route('sports.positions.index', $sport))->assertInertia(fn (Assert $page) => $page->component('positions/Index')->where('positions.0.name', 'Prop')->where('positions.1.name', 'Later'));
    $this->get(route('sports.positions.show', [$sport, $position]))->assertInertia(fn (Assert $page) => $page->component('positions/Show')->where('position.default_number', 8)->where('position.preview_x', 10));
    $this->get(route('sports.positions.edit', [$sport, $position]))->assertInertia(fn (Assert $page) => $page->component('positions/Form')->where('position.default_number', 8));
    $this->put(route('sports.positions.update', [$sport, $position]), [...$data, 'name' => 'Hooker'])->assertRedirect(route('sports.positions.index', $sport));
    expect($position->refresh()->name)->toBe('Hooker');
    $this->delete(route('sports.positions.destroy', [$sport, $position]))->assertRedirect(route('sports.positions.index', $sport));
    $this->assertSoftDeleted($position);
});

test('position fields are required and integers must fit the database', function () {
    $sport = Sport::factory()->create();
    $this->post(route('sports.positions.store', $sport), [])->assertSessionHasErrors(['name', 'description', 'preview_x', 'preview_y', 'sort_order', 'default_number']);
    $this->post(route('sports.positions.store', $sport), ['name' => 'A', 'description' => 'B', 'preview_x' => 2147483648, 'preview_y' => 'invalid', 'sort_order' => 0, 'default_number' => 0])->assertSessionHasErrors(['preview_x', 'preview_y']);
    expect($sport->positions()->count())->toBe(0);
});

test('positions cannot be accessed through a different sport', function () {
    $sport = Sport::factory()->create();
    $position = Position::factory()->create();
    $this->get(route('sports.positions.show', [$sport, $position]))->assertNotFound();
    $this->get(route('sports.positions.edit', [$sport, $position]))->assertNotFound();
    $this->put(route('sports.positions.update', [$sport, $position]), [])->assertNotFound();
    $this->delete(route('sports.positions.destroy', [$sport, $position]))->assertNotFound();
    expect($position->refresh()->trashed())->toBeFalse();
});

test('the positions index shows a shortened description', function () {
    $this->withoutVite();
    $sport = Sport::factory()->create();
    Position::factory()->for($sport)->create(['description' => str_repeat('A', 170)]);

    $this->get(route('sports.positions.index', $sport))->assertInertia(fn (Assert $page) => $page
        ->where('positions.0.description', str_repeat('A', 160).'...')
        ->missing('positions.0.default_number'));
});
