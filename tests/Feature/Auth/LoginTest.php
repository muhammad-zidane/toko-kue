<?php

use App\Models\User;

it('shows login page', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

it('logs in with valid credentials', function () {
    $user = User::factory()->create([
        'password' => bcrypt('Password@123'),
    ]);

    $response = $this->post('/login', [
        'email'    => $user->email,
        'password' => 'Password@123',
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($user);
});

it('fails with wrong password', function () {
    $user = User::factory()->create([
        'password' => bcrypt('Correct@Pass1'),
    ]);

    $response = $this->post('/login', [
        'email'    => $user->email,
        'password' => 'Wrong@Pass1',
    ]);

    $response->assertSessionHasErrors();
    $this->assertGuest();
});

it('fails with unregistered email', function () {
    $response = $this->post('/login', [
        'email'    => 'notexist@example.com',
        'password' => 'Password@123',
    ]);

    $response->assertSessionHasErrors();
    $this->assertGuest();
});

it('redirects authenticated user away from login', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/login');

    $response->assertRedirect('/dashboard');
});
