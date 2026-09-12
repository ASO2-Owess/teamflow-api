<script setup lang="ts">
import { computed, nextTick, onMounted, ref, watch } from "vue";
import { useTeamsStore } from "@/stores/teams";
import { useTasksStore } from "@/stores/tasks";
import type { TaskStatus } from "@/types";

const teamsStore = useTeamsStore();
const tasksStore = useTasksStore();
const newTaskTitle = ref("");
const newTaskAssignee = ref<number | null>(null);
const newTeamName = ref("");
const showTeamForm = ref(false);
const creatingTeam = ref(false);

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

const completedCount = computed(() => tasksStore.tasks.filter((task) => task.status === "done").length);
const totalCount = computed(() => tasksStore.tasks.length);
const progress = computed(() => totalCount.value ? Math.round((completedCount.value / totalCount.value) * 100) : 0);
const highPriorityCount = computed(() => tasksStore.tasks.filter((task) => task.priority === "high" && task.status !== "done").length);

const statusOptions: { value: TaskStatus; label: string }[] = [
  { value: "todo", label: "À faire" },
  { value: "in_progress", label: "En cours" },
  { value: "done", label: "Terminé" },
];

async function addTask() {
  if (!newTaskTitle.value.trim() || !teamsStore.activeTeamId) return;
  await tasksStore.createTask(teamsStore.activeTeamId, newTaskTitle.value.trim(), newTaskAssignee.value);
  newTaskTitle.value = "";
  newTaskAssignee.value = null;
}

async function addTeam() {
  if (!newTeamName.value.trim()) return;
  creatingTeam.value = true;
  try {
    await teamsStore.createTeam(newTeamName.value.trim());
    newTeamName.value = "";
    showTeamForm.value = false;
    await nextTick();
    await tasksStore.fetchTasks(teamsStore.activeTeamId!);
  } finally {
    creatingTeam.value = false;
  }
}

</script>

<template>
  <div class="space-y-7">
    <header class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <p class="eyebrow mb-2">Vue d'ensemble</p>
        <h1 class="display-font text-3xl font-bold tracking-tight text-[#17263b] sm:text-4xl">Tableau des tâches</h1>
        <p class="mt-2 max-w-xl text-sm leading-6 text-slate-500">Gardez le cap sur ce qui compte, équipe par équipe.</p>
      </div>
      <div class="flex w-full flex-col gap-2 sm:w-auto sm:items-end">
        <div class="flex w-full gap-2 sm:w-auto">
          <select v-model.number="teamsStore.activeTeamId" class="input min-w-0 sm:w-56">
            <option v-for="team in teamsStore.teams" :key="team.id" :value="team.id">{{ team.name }}</option>
          </select>
          <button class="btn-ghost shrink-0 border border-slate-200 bg-white" type="button" @click="showTeamForm = !showTeamForm">+ Équipe</button>
        </div>
        <form v-if="showTeamForm" class="flex w-full gap-2 sm:w-80" @submit.prevent="addTeam">
          <input v-model="newTeamName" class="input" placeholder="Nom de l'équipe" autofocus />
          <button class="btn-primary shrink-0" type="submit" :disabled="creatingTeam">Créer</button>
        </form>
      </div>
    </header>

    <section v-if="activeTeam" class="relative overflow-hidden rounded-2xl bg-[#233b59] p-6 text-white shadow-xl shadow-[#233b59]/10 sm:p-8">
      <div class="relative z-10 max-w-2xl">
        <p class="eyebrow text-[#f6a08d]">Équipe active</p>
        <h2 class="display-font mt-2 text-2xl font-bold sm:text-3xl">{{ activeTeam.name }}</h2>
        <p class="mt-2 text-sm text-slate-300">{{ totalCount ? `${completedCount} tâche${completedCount > 1 ? 's' : ''} terminée${completedCount > 1 ? 's' : ''} sur ${totalCount}` : 'Votre tableau est prêt à accueillir les premières tâches.' }}</p>
        <div class="mt-6 max-w-md">
          <div class="mb-2 flex justify-between text-xs font-semibold text-slate-300"><span>Progression</span><span>{{ progress }}%</span></div>
          <div class="h-2 overflow-hidden rounded-full bg-white/10"><div class="h-full rounded-full bg-[#f28b76] transition-all duration-500" :style="{ width: `${progress}%` }"></div></div>
        </div>
      </div>
      <div class="absolute -right-10 -top-16 h-56 w-56 rounded-full border-[28px] border-[#f28b76]/15"></div>
      <div class="absolute -bottom-24 right-20 h-44 w-44 rounded-full border-[18px] border-white/5"></div>
    </section>

    <div class="grid gap-3 sm:grid-cols-3">
      <div class="card flex items-center justify-between"><div><p class="eyebrow">Total</p><p class="display-font mt-1 text-2xl font-bold">{{ totalCount }}</p></div><span class="metric-icon bg-[#e9f0ff] text-[#476bc6]">◷</span></div>
      <div class="card flex items-center justify-between"><div><p class="eyebrow">En cours</p><p class="display-font mt-1 text-2xl font-bold">{{ tasksStore.byStatus('in_progress').length }}</p></div><span class="metric-icon bg-[#fff1df] text-[#d89036]">↗</span></div>
      <div class="card flex items-center justify-between"><div><p class="eyebrow">Priorité haute</p><p class="display-font mt-1 text-2xl font-bold">{{ highPriorityCount }}</p></div><span class="metric-icon bg-[#ffebe7] text-[#e56f5b]">!</span></div>
    </div>

    <form v-if="activeTeam" id="new-task" class="card scroll-mt-6 space-y-3" @submit.prevent="addTask">
      <div class="flex items-center justify-between"><div><p class="eyebrow">Action rapide</p><h2 class="display-font mt-1 text-lg font-bold text-[#17263b]">Ajouter une tâche</h2></div><span class="rounded-lg bg-[#fff0ec] px-2 py-1 text-[10px] font-bold uppercase tracking-wide text-[#dc604b]">{{ activeTeam.name }}</span></div>
      <div class="flex flex-col gap-2 sm:flex-row">
        <input v-model="newTaskTitle" placeholder="Ex. Corriger le flux de connexion…" class="input" />
        <select v-model.number="newTaskAssignee" class="input sm:max-w-52">
          <option :value="null">Sans responsable</option>
          <option v-for="member in activeTeam.members ?? []" :key="member.id" :value="member.id">{{ member.name }}</option>
        </select>
        <button type="submit" class="btn-primary shrink-0">+ Ajouter</button>
      </div>
    </form>

    <div class="grid gap-4 lg:grid-cols-3">
      <div v-for="col in columns" :key="col.key" class="rounded-2xl border border-slate-200/80 bg-[#edf1f6] p-3.5">
        <div class="mb-4 flex items-center justify-between px-1"><h2 class="text-sm font-bold text-slate-700">{{ col.label }}</h2><span class="grid h-6 min-w-6 place-items-center rounded-full bg-white px-1.5 text-xs font-bold text-slate-400">{{ tasksStore.byStatus(col.key).length }}</span></div>
        <div class="min-h-[180px] space-y-2.5">
          <div
            v-for="task in tasksStore.byStatus(col.key)"
            :key="task.id"
            class="task-card rounded-xl border border-slate-200/80 bg-white p-4 text-sm shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
          >
            <div class="mb-3 flex items-start justify-between gap-3"><p class="font-semibold leading-5 text-slate-700">{{ task.title }}</p><span class="h-2 w-2 shrink-0 rounded-full" :class="task.priority === 'high' ? 'bg-[#ef765e]' : task.priority === 'medium' ? 'bg-[#e6a84d]' : 'bg-[#9db0c7]'" aria-label="Priorité"></span></div>
            <div class="mb-3 flex items-center gap-2 text-xs text-slate-500">
              <span class="grid h-6 w-6 place-items-center rounded-full bg-[#e9f0ff] text-[10px] font-bold text-[#476bc6]">{{ task.assignee?.name?.slice(0, 1).toUpperCase() ?? '?' }}</span>
              <span class="truncate">{{ task.assignee?.name ?? 'Non assignée' }}</span>
            </div>
            <div class="flex items-center justify-between gap-2">
              <span
                class="rounded-md px-2 py-1 text-[10px] font-bold uppercase tracking-wide"
                :class="{
                  'bg-red-50 text-red-600': task.priority === 'high',
                  'bg-amber-50 text-amber-600': task.priority === 'medium',
                  'bg-slate-100 text-slate-500': task.priority === 'low',
                }"
              >
                {{ task.priority === 'high' ? 'Urgente' : task.priority === 'medium' ? 'Moyenne' : 'Basse' }}
              </span>
              <select :value="task.status" class="status-select" aria-label="Modifier le statut" @change="tasksStore.updateStatus(task, ($event.target as HTMLSelectElement).value as TaskStatus)">
                <option v-for="status in statusOptions" :key="status.value" :value="status.value">{{ status.label }}</option>
              </select>
            </div>
          </div>
          <p v-if="tasksStore.byStatus(col.key).length === 0" class="px-2 py-8 text-center text-xs text-slate-400">Aucune tâche ici</p>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.metric-icon {
  @apply grid h-10 w-10 place-items-center rounded-xl text-lg font-bold;
}

.status-select {
  @apply cursor-pointer rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-[11px] font-semibold text-slate-600 outline-none transition focus:border-[#ef765e] focus:ring-2 focus:ring-[#ef765e]/10;
}
</style>
