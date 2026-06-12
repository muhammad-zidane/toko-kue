<?php

use App\Models\User;

it('shows register page', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

it('registers a new user successfully', function () {
    $response = $this->post('/register', [
        'name'                  => 'Test User',
        'email'                 => 'test@example.com',
        'password'              => 'Password@123',
        'password_confirmation' => 'Password@123',
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
});

it('fails with duplicate email', function () {
    User::factory()->create(['email' => 'existing@example.com']);

    $response = $this->post('/register', [
        'name'                  => 'Test User',
        'email'                 => 'existing@example.com',
        'password'              => 'Password@123',
        'password_confirmation' => 'Password@123',
    ]);

    $response->assertSessionHasErrors('email');
});

it('fails with weak password', function () {
    $response = $this->post('/register', [
        'name'                  => 'Test User',
        'email'                 => 'test@example.com',
        'password'              => 'short',
        'password_confirmation' => 'short',
    ]);

    $response->assertSessionHasErrors('password');
});

it('fails with mismatched password confirmation', function () {
    $response = $this->post('/register', [
        'name'                  => 'Test User',
        'email'                 => 'test@example.com',
        'password'              => 'Password@123',
        'password_confirmation' => 'different123',
    ]);

    $response->assertSessionHasErrors('password');
});

it('fails with empty fields', function () {
    $response = $this->post('/register', []);

    $response->assertSessionHasErrors(['name', 'email', 'password']);
});
