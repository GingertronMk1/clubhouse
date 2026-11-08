<?php

use App\Models\Club;
use App\Models\Sport;
use Inertia\Testing\AssertableInertia as Assert;

test('the home page summarizes active sports and clubs in name order', function () {
    Sport::factory()->create(['name' => 'Rugby', 'description' => 'A team sport']);
    Sport::factory()->create(['name' => 'Cricket']);
    Sport::factory()->create(['name' => 'Deleted sport'])->delete();
    Club::factory()->create(['name' => 'Town Club', 'description' => 'A local club']);
    Club::factory()->create(['name' => 'Deleted club'])->delete();

    $this->get(route('home'))->assertOk()->assertInertia(fn (Assert $page) => $page
        ->component('Welcome')
        ->has('sports', 2)
        ->where('sports.0.name', 'Cricket')
        ->where('sports.1.name', 'Rugby')
        ->where('sports.1.description', 'A team sport')
        ->has('clubs', 1)
        ->where('clubs.0.name', 'Town Club')
        ->where('clubs.0.description', 'A local club'));
});

test('the home page handles an empty clubhouse', function () {
    $this->get(route('home'))->assertOk()->assertInertia(fn (Assert $page) => $page
        ->component('Welcome')
        ->has('sports', 0)
        ->has('clubs', 0));
});
