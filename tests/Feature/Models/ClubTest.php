<?php

use App\Models\Club;
use Illuminate\Support\Str;

test('clubs persist their details', function () {
    $club = Club::factory()->create([
        'name' => 'Town Club',
        'description' => 'A local club.',
    ]);

    $savedClub = Club::findOrFail($club->id);

    expect(Str::isUuid($club->id, version: 7))->toBeTrue();
    expect($savedClub->name)->toBe('Town Club');
    expect($savedClub->description)->toBe('A local club.');
});

test('deleted clubs are retained and can be restored', function () {
    $club = Club::factory()->create();

    $club->delete();

    $this->assertSoftDeleted($club);
    expect(Club::find($club->id))->toBeNull();

    $club->restore();

    $this->assertNotSoftDeleted($club);
    expect(Club::findOrFail($club->id)->trashed())->toBeFalse();
});
