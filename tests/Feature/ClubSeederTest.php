<?php

use App\Models\Club;
use Database\Seeders\ClubSeeder;

test('club seeding includes the requested competitions and can be repeated', function () {
    $this->seed(ClubSeeder::class);
    $this->seed(ClubSeeder::class);

    expect(Club::count())->toBe(128);
    expect(Club::where('description', 'Rugby league club in the 2026 Super League.')->count())->toBe(14);
    expect(Club::where('description', 'Rugby league club in the 2026 NRL.')->count())->toBe(17);
    expect(Club::where('description', 'Rugby union club in the 2026–27 English PREM.')->count())->toBe(10);
    expect(Club::where('description', 'Rugby union club in the 2026–27 United Rugby Championship.')->count())->toBe(16);
    expect(Club::where('description', 'American football team in the 2026 NFL.')->count())->toBe(32);
    expect(Club::where('description', 'Touch rugby representative at the 2024 Touch World Cup.')->count())->toBe(39);
    expect(Club::whereIn('name', ['York Knights', 'Dolphins', 'Newcastle Red Bulls', 'Zebre Parma', 'Kansas City Chiefs', 'Middle East Touch'])->count())->toBe(6);
});

test('club seeding preserves existing records including deleted clubs', function () {
    $existing = Club::factory()->create(['name' => 'Leeds Rhinos', 'description' => 'Custom description']);
    $deleted = Club::factory()->create(['name' => 'England Touch']);
    $deleted->delete();

    $this->seed(ClubSeeder::class);

    expect($existing->fresh()->description)->toBe('Custom description');
    expect(Club::withTrashed()->where('name', 'England Touch')->count())->toBe(1);
    $this->assertSoftDeleted($deleted);
});
