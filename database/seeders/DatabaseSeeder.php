<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::factory()->create([
            'name' => 'Owess Admin',
            'email' => 'admin@teamflow.test',
        ]);

        $team = Team::factory()->for($owner, 'owner')->create([
            'name' => 'TeamFlow Demo',
        ]);

        $team->members()->attach($owner->id, ['role' => 'admin']);

        $members = User::factory()->count(3)->create();

        foreach ($members as $member) {
            $team->members()->attach($member->id, ['role' => 'member']);
        }

        Task::factory()
            ->count(10)
            ->for($team)
            ->for($owner, 'creator')
            ->create();
    }
}
