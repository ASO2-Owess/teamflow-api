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

        $assignees = [$owner, ...$members->all()];
        $demoTasks = [
            ['title' => 'Initialiser le projet Vue 3', 'status' => 'done', 'priority' => 'high'],
            ['title' => 'Concevoir le modèle de données', 'status' => 'done', 'priority' => 'medium'],
            ['title' => 'Brancher l’authentification Sanctum', 'status' => 'in_progress', 'priority' => 'high'],
            ['title' => 'Créer le tableau Kanban', 'status' => 'in_progress', 'priority' => 'high'],
            ['title' => 'Ajouter les tests des tâches', 'status' => 'todo', 'priority' => 'medium'],
            ['title' => 'Préparer la documentation API', 'status' => 'todo', 'priority' => 'low'],
        ];

        foreach ($demoTasks as $index => $task) {
            Task::factory()->create([
                ...$task,
                'team_id' => $team->id,
                'created_by' => $owner->id,
                'assigned_to' => $assignees[$index % count($assignees)]->id,
            ]);
        }
    }
}
