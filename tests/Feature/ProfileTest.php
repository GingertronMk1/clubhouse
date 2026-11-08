<?php

use App\Models\Club;
use App\Models\User;
use App\Permission;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to log in before viewing a profile', function () {
    $this->get(route('profile.show'))->assertRedirect(route('login'));
});

test('the profile shows only the current user and their clubs', function () {
    $user = User::factory()->create(['name' => 'Alex Smith', 'email' => 'alex@example.com']);
    $club = Club::factory()->create(['name' => 'Alpha Club']);
    $otherClub = Club::factory()->create(['name' => 'Other Club']);
    $user->clubs()->attach($club, ['permissions' => [Permission::View->value]]);
    User::factory()->create()->clubs()->attach($otherClub, ['permissions' => []]);

    $this->actingAs($user)->get(route('profile.show'))->assertOk()->assertInertia(fn (Assert $page) => $page
        ->component('profile/Show')
        ->where('user.name', 'Alex Smith')
        ->where('user.email', 'alex@example.com')
        ->missing('user.password')
        ->has('clubs', 1)
        ->where('clubs.0.name', 'Alpha Club')
        ->where('clubs.0.can_view', true));
});

test('club membership without view permission is shown without a club link', function () {
    $user = User::factory()->create();
    $club = Club::factory()->create();
    $user->clubs()->attach($club, ['permissions' => []]);

    $this->actingAs($user)->get(route('profile.show'))->assertInertia(fn (Assert $page) => $page
        ->has('clubs', 1)
        ->where('clubs.0.can_view', false));
});
