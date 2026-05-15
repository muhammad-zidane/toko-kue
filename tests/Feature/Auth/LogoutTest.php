<?php

use App\Models\User;

it('logs out successfully', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $response->assertRedirect('/');
    $this->assertGuest();
});

it('cannot logout via GET', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/logout');

    $response->assertStatus(405);
});
