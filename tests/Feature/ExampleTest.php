<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

test('returns a successful response', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
});

test('production password defaults reject weak passwords and accept strong uncompromised passwords', function () {
    $originalEnvironment = app()->environment();
    app()->instance('env', 'production');
    Http::fake(['api.pwnedpasswords.com/*' => Http::response('', 200)]);

    try {
        $weak = Validator::make(
            ['password' => 'short'],
            ['password' => [Password::default()]],
        );
        $strong = Validator::make(
            ['password' => 'StrongPassword42!'],
            ['password' => [Password::default()]],
        );

        expect($weak->fails())->toBeTrue();
        expect($strong->passes())->toBeTrue();
    } finally {
        app()->instance('env', $originalEnvironment);
    }
});
