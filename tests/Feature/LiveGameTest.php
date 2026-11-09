<?php

use Inertia\Testing\AssertableInertia as Assert;

test('the live game page is accessible', function () {
    $this->get(route('live-game'))->assertOk()->assertInertia(fn (Assert $page) => $page
        ->component('LiveGame'));
});
