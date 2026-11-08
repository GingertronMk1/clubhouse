<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;

test('guests can view the login page', function () {
    $this->get(route('login'))->assertOk()->assertInertia(fn (Assert $page) => $page->component('auth/Login')->where('auth.user', null));
});

test('users can log in and log out', function () {
    $user = User::factory()->create(['password' => Hash::make('password')]);

    $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])->assertRedirect(route('profile.show'));
    $this->assertAuthenticatedAs($user);
    $this->get(route('profile.show'))->assertInertia(fn (Assert $page) => $page->where('auth.user.id', $user->id));

    $this->post(route('logout'))->assertRedirect(route('home'));
    $this->assertGuest();
});

test('invalid credentials are rejected without authenticating', function () {
    $user = User::factory()->create(['password' => Hash::make('password')]);

    $this->from(route('login'))->post(route('login.store'), ['email' => $user->email, 'password' => 'wrong'])->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('deleted users cannot log in', function () {
    $user = User::factory()->create(['password' => Hash::make('password')]);
    $user->delete();

    $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('login attempts are rate limited', function () {
    for ($attempt = 0; $attempt < 6; $attempt++) {
        $this->post(route('login.store'), ['email' => 'unknown@example.com', 'password' => 'wrong'])->assertSessionHasErrors('email');
    }

    $this->post(route('login.store'), ['email' => 'unknown@example.com', 'password' => 'wrong'])->assertTooManyRequests();
});
