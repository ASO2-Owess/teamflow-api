<script setup lang="ts">
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";

const router = useRouter();
const auth = useAuthStore();

const mode = ref<"login" | "register">("login");
const name = ref("");
const email = ref("");
const password = ref("");
const passwordConfirmation = ref("");
const error = ref<string | null>(null);
const submitting = ref(false);

async function submit() {
  error.value = null;
  submitting.value = true;
  try {
    if (mode.value === "login") {
      await auth.login(email.value, password.value);
    } else {
      await auth.register(name.value, email.value, password.value, passwordConfirmation.value);
    }
    router.push({ name: "board" });
  } catch (e: any) {
    error.value = e?.response?.data?.message ?? "Une erreur est survenue.";
  } finally {
    submitting.value = false;
  }
}
</script>

<template>
  <div class="mx-auto mt-16 max-w-sm">
    <h1 class="mb-1 text-2xl font-semibold text-brand-700">TeamFlow</h1>
    <p class="mb-6 text-sm text-slate-500">
      {{ mode === "login" ? "Connecte-toi à ton espace." : "Crée un compte." }}
    </p>

    <form class="card space-y-3" @submit.prevent="submit">
      <div v-if="mode === 'register'">
        <label class="mb-1 block text-sm font-medium">Nom</label>
        <input v-model="name" type="text" required class="input" />
      </div>

      <div>
        <label class="mb-1 block text-sm font-medium">Email</label>
        <input v-model="email" type="email" required class="input" />
      </div>

      <div>
        <label class="mb-1 block text-sm font-medium">Mot de passe</label>
        <input v-model="password" type="password" required class="input" />
      </div>

      <div v-if="mode === 'register'">
        <label class="mb-1 block text-sm font-medium">Confirmation</label>
        <input v-model="passwordConfirmation" type="password" required class="input" />
      </div>

      <p v-if="error" class="text-sm text-red-600">{{ error }}</p>

      <button type="submit" :disabled="submitting" class="btn-primary w-full">
        {{ mode === "login" ? "Se connecter" : "S'inscrire" }}
      </button>
    </form>

    <button
      class="mt-3 text-sm text-brand-600 hover:underline"
      @click="mode = mode === 'login' ? 'register' : 'login'"
    >
      {{ mode === "login" ? "Pas encore de compte ? S'inscrire" : "Déjà un compte ? Se connecter" }}
    </button>
  </div>
</template>
