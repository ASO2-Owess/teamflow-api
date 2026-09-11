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
  <div class="space-y-6">
    <h1 class="text-xl font-semibold">Mes équipes</h1>

    <form class="flex gap-2" @submit.prevent="submit">
      <input v-model="newTeamName" placeholder="Nom de la nouvelle équipe" class="input" />
      <button type="submit" :disabled="creating" class="btn-primary shrink-0">Créer</button>
    </form>

    <div v-if="teamsStore.loading" class="text-sm text-slate-500">Chargement…</div>

    <ul class="grid gap-3 sm:grid-cols-2">
      <li v-for="team in teamsStore.teams" :key="team.id" class="card">
        <p class="font-medium">{{ team.name }}</p>
        <p class="text-sm text-slate-500">{{ team.members_count ?? 0 }} membre(s)</p>
      </li>
    </ul>

    <p v-if="!teamsStore.loading && teamsStore.teams.length === 0" class="text-sm text-slate-500">
      Aucune équipe pour le moment — crée la première ci-dessus.
    </p>
  </div>
</template>
