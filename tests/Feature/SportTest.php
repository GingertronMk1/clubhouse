<?php

use App\Models\Position;
use App\Models\Sport;
use App\Models\SportEvent;
use Database\Seeders\SportSeeder;
use Inertia\Testing\AssertableInertia as Assert;

test('sports and their events can be created, viewed, updated and deleted', function () {
    $this->withoutVite();
    $this->post(route('sports.store'), [
        'name' => 'Rugby', 'description' => 'A team sport',
        'events' => [['name' => 'Try', 'description' => 'Ground the ball', 'score' => 5, 'is_team_event' => false]],
    ])->assertRedirect(route('sports.index'));

    $sport = Sport::query()->firstOrFail();
    expect($sport->events->sole())->toBeInstanceOf(SportEvent::class)
        ->and($sport->events->sole()->score)->toBe(5)
        ->and($sport->events->sole()->is_team_event)->toBeFalse();
    $this->get(route('sports.index'))->assertInertia(fn (Assert $page) => $page
        ->component('sports/Index')->where('sports.0.name', 'Rugby')->missing('sports.0.events'));
    $this->get(route('sports.show', $sport))->assertInertia(fn (Assert $page) => $page
        ->component('sports/Show')->where('sport.name', 'Rugby')->where('sport.events.0.name', 'Try')->where('sport.events.0.is_team_event', false));
    $this->get(route('sports.edit', $sport))->assertInertia(fn (Assert $page) => $page
        ->component('sports/Form')->where('sport.name', 'Rugby')->where('sport.events.0.is_team_event', false));

    $this->put(route('sports.update', $sport), [
        'name' => 'Rugby union', 'description' => 'Updated',
        'events' => [['name' => 'Conversion', 'description' => '', 'score' => 2, 'is_team_event' => true]],
    ])->assertRedirect(route('sports.index'));
    expect($sport->refresh()->name)->toBe('Rugby union');
    expect($sport->events->sole()->name)->toBe('Conversion')
        ->and($sport->events->sole()->is_team_event)->toBeTrue();
    $this->delete(route('sports.destroy', $sport))->assertRedirect(route('sports.index'));
    $this->assertSoftDeleted($sport);
});

test('sport events entries are validated', function () {
    $this->post(route('sports.store'), [
        'name' => '', 'description' => '',
        'events' => [['name' => '', 'description' => '', 'score' => 'invalid', 'is_team_event' => 'invalid']],
    ])->assertSessionHasErrors(['name', 'description', 'events.0.name', 'events.0.score', 'events.0.is_team_event']);
    expect(Sport::query()->count())->toBe(0);
});

test('the sport creation page displays an empty form', function () {
    $this->withoutVite();

    $this->get(route('sports.create'))->assertInertia(fn (Assert $page) => $page
        ->component('sports/Form')->missing('sport'));
});

test('sport fixtures include events and can be seeded repeatedly', function () {
    $this->seed(SportSeeder::class);
    $this->seed(SportSeeder::class);

    expect(Sport::query()->count())->toBe(4);
    expect(Sport::query()->orderBy('name')->pluck('name')->all())->toBe([
        'American Football', 'Rugby League', 'Rugby Union', 'Touch Rugby',
    ]);
    expect(Sport::query()->where('name', 'Rugby League')->firstOrFail()->events->first()->only(['name', 'score']))
        ->toBe(['name' => 'Try', 'score' => 4]);
    expect(Sport::query()->where('name', 'Rugby Union')->firstOrFail()->events->first()->only(['name', 'score']))
        ->toBe(['name' => 'Try', 'score' => 5]);
    expect(Sport::query()->where('name', 'Touch Rugby')->firstOrFail()->events->first()->only(['name', 'score']))
        ->toBe(['name' => 'Touchdown', 'score' => 1]);
    expect(Sport::query()->where('name', 'American Football')->firstOrFail()->events->first()->only(['name', 'score']))
        ->toBe(['name' => 'Touchdown', 'score' => 6]);
});

test('the sports index shows a shortened description', function () {
    $this->withoutVite();
    Sport::factory()->create(['description' => str_repeat('A', 170)]);

    $this->get(route('sports.index'))->assertInertia(fn (Assert $page) => $page
        ->where('sports.0.description', str_repeat('A', 160).'...'));
});

test('the sport detail page includes only its positions in display order', function () {
    $this->withoutVite();
    $sport = Sport::factory()->create();
    Position::factory()->for($sport)->create(['name' => 'Second', 'sort_order' => 2]);
    Position::factory()->for($sport)->create(['name' => 'First', 'sort_order' => 1, 'preview_x' => 25, 'preview_y' => 75, 'default_number' => 10]);
    Position::factory()->create(['name' => 'Other sport']);

    $this->get(route('sports.show', $sport))->assertInertia(fn (Assert $page) => $page
        ->component('sports/Show')
        ->has('positions', 2)
        ->where('positions.0.name', 'First')
        ->where('positions.0.preview_x', 25)
        ->where('positions.0.preview_y', 75)
        ->where('positions.0.default_number', 10)
        ->where('positions.1.name', 'Second'));
});

test('sport events default to zero and belong to their sport', function () {
    $sport = Sport::factory()->create();
    $event = $sport->events()->create(['name' => 'Foul', 'description' => 'A foul']);

    expect($event->refresh()->score)->toBe(0)
        ->and($event->is_team_event)->toBeTrue()
        ->and($event->sport->is($sport))->toBeTrue();
});
