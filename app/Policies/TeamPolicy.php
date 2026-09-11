<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;

class TeamPolicy
{
    public function view(User $user, Team $team): bool
    {
        return $user->roleInTeam($team) !== null;
    }

    public function update(User $user, Team $team): bool
    {
        return in_array($user->roleInTeam($team), ['admin', 'manager'], true);
    }

    public function delete(User $user, Team $team): bool
    {
        return $team->owner_id === $user->id;
    }
}
