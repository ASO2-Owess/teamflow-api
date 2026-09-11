<?php

use App\Models\Team;

it('lets an authenticated user create a team and become its admin', function () {
    $user = actingAsUser();

    $response = $this->postJson('/api/teams', ['name' => 'Squad Alpha']);

    $response->assertCreated()
        ->assertJsonPath('data.name', 'Squad Alpha')
        ->assertJsonPath('data.owner.id', $user->id);

    $this->assertDatabaseHas('team_user', [
        'user_id' => $user->id,
        'role' => 'admin',
    ]);
});

it('prevents a non-member from viewing a team', function () {
    $team = Team::factory()->create();
    actingAsUser();

    $this->getJson("/api/teams/{$team->id}")->assertForbidden();
});

it('lets an admin add a member with a chosen role', function () {
    $admin = actingAsUser();
    $team = Team::factory()->for($admin, 'owner')->create();
    $team->members()->attach($admin->id, ['role' => 'admin']);

    $newMember = \App\Models\User::factory()->create();

    $response = $this->postJson("/api/teams/{$team->id}/members", [
        'user_id' => $newMember->id,
        'role' => 'manager',
    ]);

    $response->assertOk();

    $this->assertDatabaseHas('team_user', [
        'team_id' => $team->id,
        'user_id' => $newMember->id,
        'role' => 'manager',
    ]);
});

it('blocks a plain member from adding other members', function () {
    $team = Team::factory()->create();
    $member = actingAsUser();
    $team->members()->attach($member->id, ['role' => 'member']);

    $response = $this->postJson("/api/teams/{$team->id}/members", [
        'user_id' => \App\Models\User::factory()->create()->id,
        'role' => 'member',
    ]);

    $response->assertForbidden();
});
