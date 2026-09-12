<script setup lang="ts">
import { computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { getToken } from "@/api";

const router = useRouter();
const route = useRoute();
const auth = useAuthStore();
const authenticated = computed(() => !!getToken());
const pageTitle = computed(() => route.name === "teams" ? "Équipes" : "Tableau des tâches");
const pageContext = computed(() => route.name === "teams" ? "Gérez les personnes et les espaces de travail." : "Suivez le travail de votre équipe, au même endroit.");

async function handleLogout() {
  await auth.logout();
  router.push({ name: "login" });
}
</script>

<template>
  <div class="min-h-screen">
    <div v-if="authenticated" class="flex min-h-screen flex-col md:flex-row">
      <aside class="flex w-full flex-col justify-between border-b border-slate-800 bg-[#17263b] px-5 py-5 text-white md:w-64 md:border-b-0 md:border-r md:px-6 md:py-7">
        <div>
          <RouterLink to="/board" class="mb-8 flex items-center gap-3">
            <span class="grid h-9 w-9 place-items-center rounded-xl bg-[#ef765e] text-sm font-bold text-white shadow-lg shadow-[#ef765e]/20">TF</span>
            <span class="display-font text-xl font-bold tracking-tight">TeamFlow</span>
          </RouterLink>
          <p class="eyebrow mb-3 text-slate-500">Espace de travail</p>
          <nav class="flex gap-2 md:flex-col">
            <RouterLink to="/board" class="nav-link" active-class="nav-link-active">
              <span class="nav-icon">▦</span> Tableau
            </RouterLink>
            <RouterLink to="/teams" class="nav-link" active-class="nav-link-active">
              <span class="nav-icon">♧</span> Équipes
            </RouterLink>
          </nav>
        </div>
        <button class="hidden items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium text-slate-400 transition hover:bg-white/5 hover:text-white md:flex" @click="handleLogout">
          <span class="text-base">↗</span> Se déconnecter
        </button>
      </aside>

      <main class="min-w-0 flex-1 px-4 py-5 sm:px-6 md:px-10 md:py-8 lg:px-14">
        <header class="mb-8 flex items-center justify-between border-b border-slate-200/80 pb-5">
          <div>
            <p class="eyebrow">TeamFlow / {{ pageTitle }}</p>
            <p class="mt-1 hidden text-sm text-slate-500 sm:block">{{ pageContext }}</p>
          </div>
          <div class="flex items-center gap-2 sm:gap-4">
            <RouterLink to="/board#new-task" class="topbar-action hidden sm:inline-flex">+ Nouvelle tâche</RouterLink>
            <span class="hidden h-8 w-px bg-slate-200 sm:block"></span>
            <span class="grid h-9 w-9 place-items-center rounded-full bg-[#e9f0ff] text-sm font-bold text-[#476bc6]">TF</span>
            <button class="topbar-action px-2 sm:hidden" title="Se déconnecter" @click="handleLogout">↗</button>
          </div>
        </header>
        <RouterView />
      </main>
      <button class="fixed bottom-5 right-5 rounded-full bg-[#17263b] px-4 py-3 text-xs font-semibold text-white shadow-xl md:hidden" @click="handleLogout">Quitter</button>
    </div>
    <main v-else class="min-h-screen px-4 py-6">
      <RouterView />
    </main>
  </div>
</template>

<style scoped>
.nav-link {
  @apply flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium text-slate-400 transition hover:bg-white/5 hover:text-white;
}

.nav-link-active {
  @apply bg-[#29415f] text-white shadow-inner;
}

.nav-icon {
  @apply grid h-6 w-6 place-items-center rounded-lg bg-white/5 text-base text-[#f28b76];
}

.topbar-action {
  @apply inline-flex items-center rounded-xl px-3 py-2 text-xs font-bold text-[#dc604b] transition hover:bg-[#fff0ec];
}
</style>
