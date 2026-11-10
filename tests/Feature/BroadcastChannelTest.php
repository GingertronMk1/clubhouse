<?php

use App\Models\User;

test('users can authorize only their own private channel', function () {
    config(['broadcasting.default' => 'pusher', 'broadcasting.connections.pusher.key' => 'test-key', 'broadcasting.connections.pusher.secret' => 'test-secret', 'broadcasting.connections.pusher.app_id' => 'test-app']);
    require base_path('routes/channels.php');
    $user = User::factory()->create(['id' => 'aaaaaaaa-aaaa-4aaa-8aaa-aaaaaaaaaaaa']);
    $other = User::factory()->create(['id' => 'bbbbbbbb-bbbb-4bbb-8bbb-bbbbbbbbbbbb']);

    $this->actingAs($user)->post('/broadcasting/auth', ['socket_id' => '123.456', 'channel_name' => 'private-App.Models.User.'.$user->id])->assertOk();
    $this->actingAs($user)->post('/broadcasting/auth', ['socket_id' => '123.456', 'channel_name' => 'private-App.Models.User.'.$other->id])->assertForbidden();
});
