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
