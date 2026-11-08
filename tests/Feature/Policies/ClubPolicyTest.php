<?php

use App\Models\Club;
use App\Models\User;
use App\Permission;
use Illuminate\Support\Facades\Gate;

test('club abilities require the matching permission on that club', function (Permission $permission) {
    $user = User::factory()->create();
    $club = Club::factory()->create();
    $otherClub = Club::factory()->create();
    $user->clubs()->attach($club, ['permissions' => [$permission->value]]);
    $user->clubs()->attach($otherClub, ['permissions' => []]);

    expect(Gate::forUser($user)->allows($permission->value, $club))->toBeTrue();
    expect(Gate::forUser($user)->allows($permission->value, $otherClub))->toBeFalse();
    expect(Gate::forUser($user)->allows($permission->value, Club::factory()->create()))->toBeFalse();

    foreach ([Permission::View, Permission::Update, Permission::Delete, Permission::Restore, Permission::ForceDelete] as $otherPermission) {
        if ($otherPermission !== $permission) {
            expect(Gate::forUser($user)->allows($otherPermission->value, $club))->toBeFalse();
        }
    }
})->with([Permission::View, Permission::Update, Permission::Delete, Permission::Restore, Permission::ForceDelete]);

test('restore and force delete permissions apply to soft deleted clubs', function (Permission $permission) {
    $user = User::factory()->create();
    $club = Club::factory()->create();
    $user->clubs()->attach($club, ['permissions' => [$permission->value]]);
    $club->delete();

    expect(Gate::forUser($user)->allows($permission->value, $club))->toBeTrue();
    expect(Gate::forUser(User::factory()->create())->allows($permission->value, $club))->toBeFalse();
})->with([Permission::Restore, Permission::ForceDelete]);

test('class level club abilities are denied without global permissions', function () {
    $user = User::factory()->create();

    expect(Gate::forUser($user)->allows('viewAny', Club::class))->toBeFalse();
    expect(Gate::forUser($user)->allows('create', Club::class))->toBeFalse();
});
