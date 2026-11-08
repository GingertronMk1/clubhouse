<?php

use App\Models\Position;
use App\Models\Scoring;
use App\Models\Sport;
use Database\Seeders\SportSeeder;
use Inertia\Testing\AssertableInertia as Assert;

test('sports and their scoring can be created, viewed, updated and deleted', function () {
    $this->withoutVite();
    $this->post(route('sports.store'), [
        'name' => 'Rugby', 'description' => 'A team sport',
        'scoring' => [['name' => 'Try', 'value' => 5]],
    ])->assertRedirect(route('sports.index'));

    $sport = Sport::query()->firstOrFail();
    expect($sport->scoring->sole())->toBeInstanceOf(Scoring::class)
        ->and($sport->scoring->sole()->value)->toBe(5);
    $this->get(route('sports.index'))->assertInertia(fn (Assert $page) => $page
        ->component('sports/Index')->where('sports.0.name', 'Rugby')->missing('sports.0.scoring'));
    $this->get(route('sports.show', $sport))->assertInertia(fn (Assert $page) => $page
        ->component('sports/Show')->where('sport.name', 'Rugby')->where('sport.scoring.0.name', 'Try'));
    $this->get(route('sports.edit', $sport))->assertInertia(fn (Assert $page) => $page
        ->component('sports/Form')->where('sport.name', 'Rugby'));

    $this->put(route('sports.update', $sport), [
        'name' => 'Rugby union', 'description' => 'Updated',
        'scoring' => [['name' => 'Conversion', 'value' => 2]],
    ])->assertRedirect(route('sports.index'));
    expect($sport->refresh()->name)->toBe('Rugby union');
    expect($sport->scoring->sole()->name)->toBe('Conversion');
    $this->delete(route('sports.destroy', $sport))->assertRedirect(route('sports.index'));
    $this->assertSoftDeleted($sport);
});

test('sport scoring entries are validated', function () {
    $this->post(route('sports.store'), [
        'name' => '', 'description' => '',
        'scoring' => [['name' => '', 'value' => 'invalid']],
    ])->assertSessionHasErrors(['name', 'description', 'scoring.0.name', 'scoring.0.value']);
    expect(Sport::query()->count())->toBe(0);
});

test('the sport creation page displays an empty form', function () {
    $this->withoutVite();

    $this->get(route('sports.create'))->assertInertia(fn (Assert $page) => $page
        ->component('sports/Form')->missing('sport'));
});

test('sport fixtures include scoring and can be seeded repeatedly', function () {
    $this->seed(SportSeeder::class);
    $this->seed(SportSeeder::class);

    expect(Sport::query()->count())->toBe(4);
    expect(Sport::query()->orderBy('name')->pluck('name')->all())->toBe([
        'American Football', 'Rugby League', 'Rugby Union', 'Touch Rugby',
    ]);
    expect(Sport::query()->where('name', 'Rugby League')->firstOrFail()->scoring->first()->toArray())
        ->toBe(['name' => 'Try', 'value' => 4]);
    expect(Sport::query()->where('name', 'Rugby Union')->firstOrFail()->scoring->first()->toArray())
        ->toBe(['name' => 'Try', 'value' => 5]);
    expect(Sport::query()->where('name', 'Touch Rugby')->firstOrFail()->scoring->first()->toArray())
        ->toBe(['name' => 'Touchdown', 'value' => 1]);
    expect(Sport::query()->where('name', 'American Football')->firstOrFail()->scoring->first()->toArray())
        ->toBe(['name' => 'Touchdown', 'value' => 6]);
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
