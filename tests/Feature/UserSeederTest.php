<?php

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

test('user seeding creates 256 named accounts and can be repeated', function () {
    $this->seed(UserSeeder::class);
    $this->seed(UserSeeder::class);

    expect(User::query()->count())->toBe(257);

    $superadmin = User::query()->where('email', 'admin@clubhouse.test')->firstOrFail();

    expect($superadmin->superadmin)->toBeTrue();
    expect(Hash::check('12345', $superadmin->password))->toBeTrue();

    foreach (User::query()->where('email', '!=', 'admin@clubhouse.test')->get() as $user) {
        expect($user->email)->toBe(Str::lower(str_replace(' ', '.', $user->name)).'@clubhouse.test');
        expect(Hash::check('password', $user->password))->toBeTrue();
    }
});
