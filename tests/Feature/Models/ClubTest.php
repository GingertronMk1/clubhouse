<?php

use App\Models\Club;
use App\Models\User;
use Illuminate\Support\Str;

test('clubs persist their details', function () {
    $club = Club::factory()->create([
        'name' => 'Town Club',
        'description' => 'A local club.',
    ]);

    $savedClub = Club::query()->findOrFail($club->id);

    expect(Str::isUuid($club->id, version: 7))->toBeTrue();
    expect($savedClub->name)->toBe('Town Club');
    expect($savedClub->description)->toBe('A local club.');
});

test('deleted clubs are retained and can be restored', function () {
    $club = Club::factory()->create();

    $club->delete();

    $this->assertSoftDeleted($club);
    expect(Club::query()->find($club->id))->toBeNull();

    $club->restore();

    $this->assertNotSoftDeleted($club);
    expect(Club::query()->findOrFail($club->id)->trashed())->toBeFalse();
});

test('club memberships store permissions and are accessible from both sides', function () {
    $club = Club::factory()->create();
    $user = User::factory()->create();

    $club->users()->attach($user, ['permissions' => ['manage-team', 'view-members']]);

    expect($club->users()->firstOrFail()->pivot->permissions)->toBe(['manage-team', 'view-members']);
    expect($user->clubs()->firstOrFail()->pivot->permissions)->toBe(['manage-team', 'view-members']);

    $user->clubs()->updateExistingPivot($club, ['permissions' => ['view-members']]);

    expect($club->users()->firstOrFail()->pivot->permissions)->toBe(['view-members']);
});
