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
  <div class="mx-auto grid min-h-[calc(100vh-3rem)] max-w-5xl items-center gap-10 py-8 lg:grid-cols-[1fr_390px] lg:gap-20">
    <section class="hidden lg:block">
      <div class="mb-8 flex items-center gap-3"><span class="grid h-10 w-10 place-items-center rounded-xl bg-[#ef765e] text-sm font-bold text-white">TF</span><span class="display-font text-2xl font-bold text-[#17263b]">TeamFlow</span></div>
      <p class="eyebrow mb-4">Le travail, en mouvement</p>
      <h1 class="display-font max-w-xl text-6xl font-bold leading-[1.04] tracking-tight text-[#17263b]">Donnez à chaque tâche une direction.</h1>
      <p class="mt-6 max-w-md text-base leading-7 text-slate-500">Un espace simple pour aligner les équipes, clarifier les priorités et faire avancer les projets.</p>
      <div class="mt-10 flex gap-3"><span class="rounded-full bg-[#e9f0ff] px-4 py-2 text-xs font-bold text-[#476bc6]">Équipes alignées</span><span class="rounded-full bg-[#fff1df] px-4 py-2 text-xs font-bold text-[#c78632]">Progrès visible</span></div>
    </section>

    <section>
      <div class="mb-7 lg:hidden"><span class="grid h-10 w-10 place-items-center rounded-xl bg-[#ef765e] text-sm font-bold text-white">TF</span><p class="display-font mt-3 text-2xl font-bold text-[#17263b]">TeamFlow</p></div>
      <p class="eyebrow mb-2">{{ mode === "login" ? "Bon retour" : "Nouveau départ" }}</p>
      <h2 class="display-font text-3xl font-bold tracking-tight text-[#17263b]">{{ mode === "login" ? "Ravi de vous revoir." : "Créez votre espace." }}</h2>
      <p class="mb-6 mt-2 text-sm text-slate-500">{{ mode === "login" ? "Connectez-vous pour retrouver votre équipe." : "Quelques secondes pour commencer à travailler ensemble." }}</p>

      <form class="card space-y-4" @submit.prevent="submit">
      <div v-if="mode === 'register'">
        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500">Nom</label>
        <input v-model="name" type="text" required class="input" />
      </div>

      <div>
        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500">Email</label>
        <input v-model="email" type="email" required class="input" />
      </div>

      <div>
        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500">Mot de passe</label>
        <input v-model="password" type="password" required class="input" />
      </div>

      <div v-if="mode === 'register'">
        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500">Confirmation</label>
        <input v-model="passwordConfirmation" type="password" required class="input" />
      </div>

      <p v-if="error" class="rounded-xl bg-red-50 px-3 py-2 text-sm text-red-600">{{ error }}</p>

      <button type="submit" :disabled="submitting" class="btn-primary w-full">
        {{ mode === "login" ? "Se connecter" : "S'inscrire" }}
      </button>
      </form>

      <button
      class="mt-4 text-sm font-semibold text-[#dc604b] hover:underline"
      @click="mode = mode === 'login' ? 'register' : 'login'"
      >
        {{ mode === "login" ? "Pas encore de compte ? S'inscrire" : "Déjà un compte ? Se connecter" }}
      </button>
    </section>
  </div>
</template>
