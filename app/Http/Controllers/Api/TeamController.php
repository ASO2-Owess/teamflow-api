<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeamRequest;
use App\Http\Resources\TeamResource;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $teams = $request->user()->teams()->with('owner')->withCount('members')->get();

        return TeamResource::collection($teams);
    }

    public function store(StoreTeamRequest $request)
    {
        $team = Team::create([
            'name' => $request->validated('name'),
            'owner_id' => $request->user()->id,
        ]);

        $team->members()->attach($request->user()->id, ['role' => 'admin']);

        return new TeamResource($team->load('owner', 'members'));
    }

    public function show(Request $request, Team $team)
    {
        $this->authorize('view', $team);

        return new TeamResource($team->load('owner', 'members'));
    }

    public function update(StoreTeamRequest $request, Team $team)
    {
        $this->authorize('update', $team);

        $team->update($request->validated());

        return new TeamResource($team->fresh(['owner', 'members']));
    }

    public function destroy(Request $request, Team $team)
    {
        $this->authorize('delete', $team);

        $team->delete();

        return response()->json(null, 204);
    }

    public function addMember(Request $request, Team $team)
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'role' => ['required', 'in:admin,manager,member'],
        ]);

        $team->members()->syncWithoutDetaching([
            $data['user_id'] => ['role' => $data['role']],
        ]);

        return new TeamResource($team->fresh(['owner', 'members']));
    }

    public function removeMember(Request $request, Team $team, User $user)
    {
        $team->members()->detach($user->id);

        return response()->json(null, 204);
    }
}
