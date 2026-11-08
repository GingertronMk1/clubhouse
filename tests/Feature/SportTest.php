<?php

use App\Models\Scoring;
use App\Models\Sport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Testing\AssertableInertia as Assert;

test('sports and their scoring can be created, viewed, updated and deleted', function () {
    $this->withoutVite();
    $this->post(route('sports.store'), [
        'name' => 'Rugby', 'description' => 'A team sport',
        'scoring' => [['name' => 'Try', 'value' => 5]],
    ])->assertRedirect(route('sports.index'));

    $sport = Sport::firstOrFail();
    expect($sport->scoring->sole())->toBeInstanceOf(Scoring::class)
        ->and($sport->scoring->sole()->value)->toBe(5);
    $this->get(route('sports.index'))->assertInertia(fn (Assert $page) => $page
        ->component('sports/Index')->where('sports.0.name', 'Rugby')->where('sports.0.scoring.0.name', 'Try'));
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
    expect(Sport::count())->toBe(0);
});
