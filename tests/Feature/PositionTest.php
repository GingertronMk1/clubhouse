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

test('the edit page includes the other positions for the same sport', function () {
    $this->withoutVite();
    $sport = Sport::factory()->create();
    $position = Position::factory()->for($sport)->create(['name' => 'Edited']);
    Position::factory()->for($sport)->create(['name' => 'Other', 'preview_x' => 25, 'preview_y' => 75, 'default_number' => 7]);
    Position::factory()->create(['name' => 'Another sport']);

    $this->get(route('sports.positions.edit', [$sport, $position]))->assertInertia(fn (Assert $page) => $page
        ->component('positions/Form')
        ->where('position.name', 'Edited')
        ->has('otherPositions', 1)
        ->where('otherPositions.0.name', 'Other')
        ->where('otherPositions.0.preview_x', 25)
        ->where('otherPositions.0.preview_y', 75)
        ->where('otherPositions.0.default_number', 7));
});

test('the creation page includes positions for the field preview', function () {
    $this->withoutVite();
    $sport = Sport::factory()->create();
    Position::factory()->for($sport)->create(['name' => 'Existing', 'preview_x' => 20]);
    Position::factory()->create();

    $this->get(route('sports.positions.create', $sport))->assertInertia(fn (Assert $page) => $page
        ->component('positions/Form')
        ->has('otherPositions', 1)
        ->where('otherPositions.0.name', 'Existing')
        ->where('otherPositions.0.preview_x', 20));
});

test('the position detail page includes only the other positions in its sport', function () {
    $this->withoutVite();
    $sport = Sport::factory()->create();
    $position = Position::factory()->for($sport)->create(['name' => 'Selected', 'preview_x' => 30, 'preview_y' => 40]);
    Position::factory()->for($sport)->create(['name' => 'Other', 'preview_x' => 25, 'preview_y' => 75, 'default_number' => 7]);
    Position::factory()->create();

    $this->get(route('sports.positions.show', [$sport, $position]))->assertInertia(fn (Assert $page) => $page
        ->component('positions/Show')
        ->where('position.preview_x', 30)
        ->where('position.preview_y', 40)
        ->has('otherPositions', 1)
        ->where('otherPositions.0.name', 'Other')
        ->where('otherPositions.0.preview_x', 25)
        ->where('otherPositions.0.preview_y', 75));
});

test('position slugs are unique within each sport and bind to the correct position', function () {
    $this->withoutVite();
    $sport = Sport::factory()->create(['name' => 'Rugby League']);
    $otherSport = Sport::factory()->create(['name' => 'Rugby Union']);
    $first = Position::factory()->for($sport)->create(['name' => 'Prop']);
    $second = Position::factory()->for($sport)->create(['name' => 'Prop']);
    $other = Position::factory()->for($otherSport)->create(['name' => 'Prop']);

    expect($first->slug)->toBe('prop')
        ->and($second->slug)->toBe('prop-2')
        ->and($other->slug)->toBe('prop')
        ->and(route('sports.positions.show', [$sport, $second]))->toEndWith('/sports/rugby-league/positions/prop-2');

    $this->get(route('sports.positions.show', [$sport, $second]))
        ->assertInertia(fn (Assert $page) => $page->where('position.id', $second->id));
    $this->get(route('sports.positions.show', [$otherSport, $other]))
        ->assertInertia(fn (Assert $page) => $page->where('position.id', $other->id));
    $this->get('/sports/rugby-union/positions/prop-2')->assertNotFound();
    $this->get(route('sports.positions.index', $sport))
        ->assertInertia(fn (Assert $page) => $page->where('positions.0.slug', $first->slug));
    $this->get(route('sports.show', $sport))
        ->assertInertia(fn (Assert $page) => $page->where('positions.0.slug', $first->slug));
});
