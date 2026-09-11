<?php

use App\Models\Task;
use App\Models\Team;
use App\Models\User;

function teamWithRole(User $user, string $role): Team
{
    $team = Team::factory()->create();
    $team->members()->attach($user->id, ['role' => $role]);

    return $team;
}

it('lets a team member create a task', function () {
    $user = actingAsUser();
    $team = teamWithRole($user, 'member');

    $response = $this->postJson("/api/teams/{$team->id}/tasks", [
        'title' => 'Écrire les tests Pest',
        'priority' => 'high',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.title', 'Écrire les tests Pest')
        ->assertJsonPath('data.status', 'todo');
});

it('lets a member update only tasks assigned to them', function () {
    $user = actingAsUser();
    $team = teamWithRole($user, 'member');

    $ownTask = Task::factory()->for($team)->for($user, 'creator')->create([
        'assigned_to' => $user->id,
    ]);

    $othersTask = Task::factory()->for($team)->for($user, 'creator')->create([
        'assigned_to' => User::factory()->create()->id,
    ]);

    $this->patchJson("/api/tasks/{$ownTask->id}", ['status' => 'done'])
        ->assertOk()
        ->assertJsonPath('data.status', 'done');

    $this->patchJson("/api/tasks/{$othersTask->id}", ['status' => 'done'])
        ->assertForbidden();
});

it('lets an admin delete any task in the team', function () {
    $admin = actingAsUser();
    $team = teamWithRole($admin, 'admin');

    $task = Task::factory()->for($team)->for($admin, 'creator')->create();

    $this->deleteJson("/api/tasks/{$task->id}")->assertNoContent();

    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});

it('filters tasks by status', function () {
    $user = actingAsUser();
    $team = teamWithRole($user, 'admin');

    Task::factory()->for($team)->for($user, 'creator')->create(['status' => 'done']);
    Task::factory()->for($team)->for($user, 'creator')->create(['status' => 'todo']);

    $response = $this->getJson("/api/teams/{$team->id}/tasks?status=done");

    $response->assertOk()->assertJsonCount(1, 'data');
});
