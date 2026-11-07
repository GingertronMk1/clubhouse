<?php

use App\Models\User;
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
