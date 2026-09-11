import { defineStore } from "pinia";
import { ref } from "vue";
import { api } from "@/api";
import type { Team } from "@/types";

export const useTeamsStore = defineStore("teams", () => {
  const teams = ref<Team[]>([]);
  const activeTeamId = ref<number | null>(null);
  const loading = ref(false);

  async function fetchTeams(): Promise<void> {
    loading.value = true;
    try {
      const { data } = await api.get<{ data: Team[] }>("/teams");
      teams.value = data.data;
      if (!activeTeamId.value && teams.value.length > 0) {
        activeTeamId.value = teams.value[0].id;
      }
    } finally {
      loading.value = false;
    }
  }

  async function createTeam(name: string): Promise<void> {
    const { data } = await api.post<{ data: Team }>("/teams", { name });
    teams.value.push(data.data);
    activeTeamId.value = data.data.id;
  }

  return { teams, activeTeamId, loading, fetchTeams, createTeam };
});
