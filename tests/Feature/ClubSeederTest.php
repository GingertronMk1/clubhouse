<?php

use App\Models\Club;
use App\Models\User;
use Database\Seeders\ClubSeeder;
use Database\Seeders\UserSeeder;

test('club seeding includes the requested competitions and can be repeated', function () {
    $this->seed(ClubSeeder::class);
    $this->seed(ClubSeeder::class);

    expect(Club::query()->count())->toBe(128);
    expect(Club::query()->where('description', 'Rugby league club in the 2026 Super League.')->count())->toBe(14);
    expect(Club::query()->where('description', 'Rugby league club in the 2026 NRL.')->count())->toBe(17);
    expect(Club::query()->where('description', 'Rugby union club in the 2026–27 English PREM.')->count())->toBe(10);
    expect(Club::query()->where('description', 'Rugby union club in the 2026–27 United Rugby Championship.')->count())->toBe(16);
    expect(Club::query()->where('description', 'American football team in the 2026 NFL.')->count())->toBe(32);
    expect(Club::query()->where('description', 'Touch rugby representative at the 2024 Touch World Cup.')->count())->toBe(39);
    expect(Club::query()->whereIn('name', ['York Knights', 'Dolphins', 'Newcastle Red Bulls', 'Zebre Parma', 'Kansas City Chiefs', 'Middle East Touch'])->count())->toBe(6);
});

test('club seeding preserves existing records including deleted clubs', function () {
    $existing = Club::factory()->create(['name' => 'Leeds Rhinos', 'description' => 'Custom description']);
    $deleted = Club::factory()->create(['name' => 'England Touch']);
    $deleted->delete();

    $this->seed(ClubSeeder::class);

    expect($existing->fresh()->description)->toBe('Custom description');
    expect(Club::query()->withTrashed()->where('name', 'England Touch')->count())->toBe(1);
    $this->assertSoftDeleted($deleted);
});

test('club seeding assigns 25 users in order and wraps around without changing existing permissions', function () {
    $this->seed(UserSeeder::class);
    $users = User::query()->orderBy('email')->pluck('id');

    $this->seed(ClubSeeder::class);

    $firstClub = Club::query()->where('name', 'Bradford Bulls')->firstOrFail();
    $eleventhClub = Club::query()->where('name', 'Warrington Wolves')->firstOrFail();
    $firstClub->users()->updateExistingPivot($users[0], ['permissions' => ['manage-team']]);

    $this->seed(ClubSeeder::class);

    expect(Club::query()->withCount('users')->get()->pluck('users_count')->unique()->all())->toBe([25]);
    expect($firstClub->users()->pluck('users.id')->sort()->values()->all())->toBe($users->slice(0, 25)->sort()->values()->all());
    expect($eleventhClub->users()->pluck('users.id')->sort()->values()->all())->toBe($users->slice(250)->concat($users->slice(0, 18))->sort()->values()->all());
    expect($firstClub->users()->whereKey($users[0])->firstOrFail()->pivot->permissions)->toBe(['manage-team']);
});
