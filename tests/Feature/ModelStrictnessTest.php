<?php

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\MissingAttributeException;
use Illuminate\Database\LazyLoadingViolationException;

test('accessing an unselected model attribute fails', function () {
    $user = User::factory()->create();
    $partialUser = User::query()->select('id')->findOrFail($user->id);

    expect(fn () => $partialUser->email)->toThrow(MissingAttributeException::class);
});

test('lazy loading relationships on a collection fails', function () {
    Team::factory()->count(2)->create();
    $team = Team::query()->without('club')->get()->firstOrFail();

    expect(fn () => $team->club)->toThrow(LazyLoadingViolationException::class);
});
