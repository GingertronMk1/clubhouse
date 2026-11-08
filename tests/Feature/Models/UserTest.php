<?php

use App\Models\Club;
use App\Models\User;
use App\Permission;
use Illuminate\Support\Str;

test('users are persisted with UUIDv7 identifiers', function () {
    $user = User::factory()->create();

    expect(Str::isUuid($user->id, version: 7))->toBeTrue();
    expect(User::findOrFail($user->id)->id)->toBe($user->id);
});

test('deleted users are retained and can be restored', function () {
    $user = User::factory()->create();

    $user->delete();

    $this->assertSoftDeleted($user);
    expect(User::find($user->id))->toBeNull();

    $user->restore();

    expect(User::findOrFail($user->id)->trashed())->toBeFalse();
    $this->assertNotSoftDeleted($user);
});

test('club permissions require every requested permission for the specified club', function () {
    $user = User::factory()->create();
    $club = Club::factory()->create();
    $otherClub = Club::factory()->create();
    $user->clubs()->attach($club, ['permissions' => [Permission::View->value, Permission::Update->value]]);
    $user->clubs()->attach($otherClub, ['permissions' => [Permission::Delete->value]]);

    expect($user->hasClubPermission($club, Permission::View))->toBeTrue();
    expect($user->hasClubPermission($club, Permission::Delete))->toBeFalse();
    expect($user->hasClubPermission($club, [Permission::View, Permission::Update]))->toBeTrue();
    expect($user->hasClubPermission($club, [Permission::View, Permission::Delete]))->toBeFalse();
    expect($user->hasClubPermission($otherClub, Permission::View))->toBeFalse();
    expect($user->hasClubPermission(Club::factory()->create(), Permission::View))->toBeFalse();
    expect($user->hasClubPermission($club, []))->toBeFalse();

    $club->delete();

    expect($user->hasClubPermission($club, Permission::View))->toBeFalse();
});

test('superadmins have all club permissions without membership', function () {
    $user = User::factory()->create();
    $club = Club::factory()->create();
    $otherClub = Club::factory()->create();

    expect($user->fresh()->superadmin)->toBeFalse();
    expect($user->hasClubPermission($club, Permission::View))->toBeFalse();

    $user->superadmin = true;
    $user->save();
    $user = $user->fresh();

    expect($user->superadmin)->toBeTrue();
    expect($user->hasClubPermission($club, Permission::View))->toBeTrue();
    expect($user->hasClubPermission($otherClub, [Permission::Update, Permission::Delete]))->toBeTrue();
    expect($user->hasClubPermission($club, []))->toBeFalse();
    expect($user->clubs()->count())->toBe(0);
});

test('superadmin status cannot be mass assigned', function () {
    $user = User::factory()->create();

    $user->fill(['superadmin' => true]);
    $user->save();

    expect($user->fresh()->superadmin)->toBeFalse();
});
