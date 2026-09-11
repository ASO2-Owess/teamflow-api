<script setup lang="ts">
import { computed } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { getToken } from "@/api";

const router = useRouter();
const auth = useAuthStore();
const authenticated = computed(() => !!getToken());

async function handleLogout() {
  await auth.logout();
  router.push({ name: "login" });
}
</script>

<template>
  <div class="min-h-screen">
    <nav v-if="authenticated" class="border-b border-slate-200 bg-white">
      <div class="mx-auto flex max-w-5xl items-center justify-between px-4 py-3">
        <div class="flex items-center gap-6">
          <span class="text-lg font-semibold text-brand-700">TeamFlow</span>
          <RouterLink to="/board" class="text-sm text-slate-600 hover:text-brand-600">Tâches</RouterLink>
          <RouterLink to="/teams" class="text-sm text-slate-600 hover:text-brand-600">Équipes</RouterLink>
        </div>
        <button class="btn-ghost" @click="handleLogout">Déconnexion</button>
      </div>
    </nav>

    <main class="mx-auto max-w-5xl px-4 py-6">
      <RouterView />
    </main>
  </div>
</template>
