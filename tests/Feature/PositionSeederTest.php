<?php

use App\Models\Sport;
use Database\Seeders\PositionSeeder;
use Database\Seeders\SportSeeder;

test('position seeder populates each sport and can be run repeatedly', function () {
    $this->seed(SportSeeder::class);
    $this->seed(PositionSeeder::class);
    $this->seed(PositionSeeder::class);

    $expected = [
        'Rugby League' => [13, 'Fullback', 1],
        'Rugby Union' => [15, 'Loosehead Prop', 1],
        'Touch Rugby' => [6, 'Left Wing', 1],
        'American Football' => [23, 'Quarterback', 1],
    ];

    foreach ($expected as $sportName => [$count, $firstPosition, $number]) {
        $sport = Sport::query()->where('name', $sportName)->firstOrFail();
        $positions = $sport->positions()->orderBy('sort_order')->get();

        expect($positions)->toHaveCount($count)
            ->and($positions->first()->name)->toBe($firstPosition)
            ->and($positions->first()->default_number)->toBe($number)
            ->and($positions->pluck('sort_order')->all())->toBe(range(1, $count));
    }

    expect(Sport::where('name', 'Rugby Union')->firstOrFail()->positions()->where('name', 'Fullback')->firstOrFail()->default_number)->toBe(15);
    expect(Sport::where('name', 'Rugby League')->firstOrFail()->positions()->where('name', 'Loose Forward')->firstOrFail()->default_number)->toBe(13);
});

test('the default database seeder includes sport positions', function () {
    $this->seed();

    expect(Sport::query()->withCount('positions')->pluck('positions_count', 'name')->all())->toEqual([
        'Rugby League' => 13,
        'Rugby Union' => 15,
        'Touch Rugby' => 6,
        'American Football' => 23,
    ]);
});

test('american football positions are grouped into field thirds by unit', function () {
    $this->seed(SportSeeder::class);
    $this->seed(PositionSeeder::class);

    $positions = Sport::where('name', 'American Football')->firstOrFail()->positions->keyBy('name');
    $units = [
        [0, 33, ['Quarterback', 'Running Back', 'Fullback', 'Wide Receiver', 'Tight End', 'Center', 'Offensive Guard', 'Offensive Tackle']],
        [34, 66, ['Defensive Tackle', 'Defensive End', 'Nose Tackle', 'Inside Linebacker', 'Outside Linebacker', 'Cornerback', 'Free Safety', 'Strong Safety']],
        [67, 100, ['Placekicker', 'Punter', 'Long Snapper', 'Holder', 'Kick Returner', 'Punt Returner', 'Gunner']],
    ];

    foreach ($units as [$minimum, $maximum, $names]) {
        foreach ($names as $name) {
            expect($positions->get($name))->not->toBeNull()
                ->and($positions->get($name)->preview_y)->toBeGreaterThanOrEqual($minimum)->toBeLessThanOrEqual($maximum);
        }
    }
});
