<?php

use App\Models\Location;
use Illuminate\Support\Str;

test('locations persist their details and additional information', function () {
    $location = Location::factory()->create([
        'name' => 'Main Ground',
        'description' => 'A place to play.',
        'additional_information' => ['facilities' => ['parking', 'showers']],
    ]);

    $savedLocation = Location::query()->findOrFail($location->id);

    expect(Str::isUuid($location->id, version: 7))->toBeTrue();
    expect($savedLocation->name)->toBe('Main Ground');
    expect($savedLocation->description)->toBe('A place to play.');
    expect($savedLocation->additional_information)->toBe(['facilities' => ['parking', 'showers']]);
});

test('deleted locations are retained and can be restored', function () {
    $location = Location::factory()->create();

    $location->delete();

    $this->assertSoftDeleted($location);
    expect(Location::query()->find($location->id))->toBeNull();

    $location->restore();

    $this->assertNotSoftDeleted($location);
    expect(Location::query()->findOrFail($location->id)->trashed())->toBeFalse();
});
