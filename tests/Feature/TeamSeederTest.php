<?php

use App\Models\Club;
use App\Models\Team;
use Database\Seeders\TeamSeeder;

test('team seeding adds the four teams to each club and can be repeated', function () {
    $clubs = Club::factory()->count(2)->create();
    $existing = Team::factory()->for($clubs[0])->create(['name' => 'First team', 'description' => 'Custom description']);
    $deleted = Team::factory()->for($clubs[1])->create(['name' => 'Social team']);
    $deleted->delete();

    $this->seed(TeamSeeder::class);
    $this->seed(TeamSeeder::class);

    foreach ($clubs as $club) {
        expect($club->teams()->withTrashed()->orderBy('name')->pluck('name')->all())->toBe([
            'First team', 'Second team', 'Social team', 'Third team',
        ]);
    }
    expect($existing->fresh()->description)->toBe('Custom description');
    $this->assertSoftDeleted($deleted);
});

test('the default database seeder adds teams to every club', function () {
    $this->seed();

    expect(Club::count())->toBeGreaterThan(0);
    expect(Club::has('teams', '!=', 4)->count())->toBe(0);
});
