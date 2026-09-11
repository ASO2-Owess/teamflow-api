<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function view(User $user, Task $task): bool
    {
        return $user->roleInTeam($task->team) !== null;
    }

    public function update(User $user, Task $task): bool
    {
        $role = $user->roleInTeam($task->team);

        // Admin/manager peuvent tout modifier ; un membre simple
        // ne peut modifier que les tâches qui lui sont assignées.
        return in_array($role, ['admin', 'manager'], true)
            || ($role === 'member' && $task->assigned_to === $user->id);
    }

    public function delete(User $user, Task $task): bool
    {
        return in_array($user->roleInTeam($task->team), ['admin', 'manager'], true);
    }
}
