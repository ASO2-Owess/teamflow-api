<script setup lang="ts">
import { computed, onMounted, ref, watch } from "vue";
import { useTeamsStore } from "@/stores/teams";
import { useTasksStore } from "@/stores/tasks";
import type { TaskStatus } from "@/types";

const teamsStore = useTeamsStore();
const tasksStore = useTasksStore();
const newTaskTitle = ref("");

const columns: { key: TaskStatus; label: string }[] = [
  { key: "todo", label: "À faire" },
  { key: "in_progress", label: "En cours" },
  { key: "done", label: "Terminé" },
];

onMounted(async () => {
  await teamsStore.fetchTeams();
  if (teamsStore.activeTeamId) {
    await tasksStore.fetchTasks(teamsStore.activeTeamId);
  }
});

watch(
  () => teamsStore.activeTeamId,
  async (teamId) => {
    if (teamId) await tasksStore.fetchTasks(teamId);
  }
);

const activeTeam = computed(() =>
  teamsStore.teams.find((t) => t.id === teamsStore.activeTeamId)
);

async function addTask() {
  if (!newTaskTitle.value.trim() || !teamsStore.activeTeamId) return;
  await tasksStore.createTask(teamsStore.activeTeamId, newTaskTitle.value.trim());
  newTaskTitle.value = "";
}

function nextStatus(status: TaskStatus): TaskStatus | null {
  if (status === "todo") return "in_progress";
  if (status === "in_progress") return "done";
  return null;
}
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-xl font-semibold">Tableau des tâches</h1>
      <select v-model.number="teamsStore.activeTeamId" class="input w-56">
        <option v-for="team in teamsStore.teams" :key="team.id" :value="team.id">
          {{ team.name }}
        </option>
      </select>
    </div>

    <form v-if="activeTeam" class="flex gap-2" @submit.prevent="addTask">
      <input v-model="newTaskTitle" placeholder="Nouvelle tâche…" class="input" />
      <button type="submit" class="btn-primary shrink-0">Ajouter</button>
    </form>

    <div class="grid gap-4 sm:grid-cols-3">
      <div v-for="col in columns" :key="col.key" class="card min-h-[200px]">
        <h2 class="mb-3 text-sm font-semibold uppercase text-slate-500">{{ col.label }}</h2>
        <div class="space-y-2">
          <div
            v-for="task in tasksStore.byStatus(col.key)"
            :key="task.id"
            class="rounded-md border border-slate-200 p-3 text-sm"
          >
            <p class="font-medium">{{ task.title }}</p>
            <div class="mt-2 flex items-center justify-between">
              <span
                class="rounded-full px-2 py-0.5 text-xs"
                :class="{
                  'bg-red-100 text-red-700': task.priority === 'high',
                  'bg-amber-100 text-amber-700': task.priority === 'medium',
                  'bg-slate-100 text-slate-600': task.priority === 'low',
                }"
              >
                {{ task.priority }}
              </span>
              <button
                v-if="nextStatus(task.status)"
                class="text-xs text-brand-600 hover:underline"
                @click="tasksStore.updateStatus(task, nextStatus(task.status)!)"
              >
                → {{ nextStatus(task.status) }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
