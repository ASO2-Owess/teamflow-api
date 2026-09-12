<script setup lang="ts">
import { onMounted, ref } from "vue";
import { useTeamsStore } from "@/stores/teams";

const teamsStore = useTeamsStore();
const newTeamName = ref("");
const creating = ref(false);

onMounted(() => {
  teamsStore.fetchTeams();
});

async function submit() {
  if (!newTeamName.value.trim()) return;
  creating.value = true;
  try {
    await teamsStore.createTeam(newTeamName.value.trim());
    newTeamName.value = "";
  } finally {
    creating.value = false;
  }
}
</script>

<template>
  <div class="space-y-7">
    <header>
      <p class="eyebrow mb-2">Organisation</p>
      <h1 class="display-font text-3xl font-bold tracking-tight text-[#17263b] sm:text-4xl">Mes équipes</h1>
      <p class="mt-2 text-sm leading-6 text-slate-500">Les espaces où vos projets prennent forme.</p>
    </header>

    <form class="card flex flex-col gap-3 sm:flex-row" @submit.prevent="submit">
      <input v-model="newTeamName" placeholder="Nom de la nouvelle équipe" class="input" />
      <button type="submit" :disabled="creating" class="btn-primary shrink-0">+ Créer une équipe</button>
    </form>

    <div v-if="teamsStore.loading" class="card text-sm text-slate-500">Chargement de vos équipes…</div>

    <ul class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
      <li v-for="team in teamsStore.teams" :key="team.id" class="card group relative overflow-hidden transition hover:-translate-y-1 hover:shadow-lg">
        <div class="mb-8 flex items-start justify-between"><span class="grid h-11 w-11 place-items-center rounded-2xl bg-[#e9f0ff] font-bold text-[#476bc6]">{{ team.name.slice(0, 2).toUpperCase() }}</span><span class="text-xs font-semibold text-slate-400">#{{ String(team.id).padStart(2, '0') }}</span></div>
        <p class="display-font text-xl font-bold text-[#17263b]">{{ team.name }}</p>
        <p class="mt-1 text-sm text-slate-500">{{ team.members_count ?? 0 }} membre(s)</p>
        <RouterLink to="/board" class="mt-5 inline-flex text-xs font-bold uppercase tracking-wide text-[#dc604b]">Ouvrir le tableau →</RouterLink>
      </li>
    </ul>

    <p v-if="!teamsStore.loading && teamsStore.teams.length === 0" class="text-sm text-slate-500">
      Aucune équipe pour le moment — crée la première ci-dessus.
    </p>
  </div>
</template>
