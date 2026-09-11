<?php

use App\Models\User;

it('allows a user to register and receive an api token', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'Akpa Owess',
        'email' => 'owess@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertCreated()
        ->assertJsonStructure(['user' => ['id', 'name', 'email'], 'token']);

    $this->assertDatabaseHas('users', ['email' => 'owess@example.com']);
});

it('rejects registration with a duplicate email', function () {
    User::factory()->create(['email' => 'taken@example.com']);

    $response = $this->postJson('/api/register', [
        'name' => 'Someone',
        'email' => 'taken@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(422);
});

it('allows a registered user to log in', function () {
    $user = User::factory()->create(['password' => bcrypt('secret123')]);

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'secret123',
    ]);

    $response->assertOk()->assertJsonStructure(['user', 'token']);
});

it('rejects login with wrong credentials', function () {
    $user = User::factory()->create();

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(422);
});

it('returns the authenticated user on /api/me', function () {
    $user = actingAsUser();

    $this->getJson('/api/me')
        ->assertOk()
        ->assertJsonPath('id', $user->id);
});

it('rejects unauthenticated access to protected routes', function () {
    $this->getJson('/api/me')->assertUnauthorized();
});
