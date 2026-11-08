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
