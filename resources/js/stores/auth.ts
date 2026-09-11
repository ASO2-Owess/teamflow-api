import { defineStore } from "pinia";
import { ref } from "vue";
import { api, getToken, setToken } from "@/api";
import type { User } from "@/types";

export const useAuthStore = defineStore("auth", () => {
  const user = ref<User | null>(null);
  const isAuthenticated = ref<boolean>(!!getToken());

  async function login(email: string, password: string): Promise<void> {
    const { data } = await api.post("/login", { email, password });
    setToken(data.token);
    user.value = data.user;
    isAuthenticated.value = true;
  }

  async function register(
    name: string,
    email: string,
    password: string,
    password_confirmation: string
  ): Promise<void> {
    const { data } = await api.post("/register", {
      name,
      email,
      password,
      password_confirmation,
    });
    setToken(data.token);
    user.value = data.user;
    isAuthenticated.value = true;
  }

  async function fetchCurrentUser(): Promise<void> {
    const { data } = await api.get<User>("/me");
    user.value = data;
    isAuthenticated.value = true;
  }

  async function logout(): Promise<void> {
    try {
      await api.post("/logout");
    } finally {
      setToken(null);
      user.value = null;
      isAuthenticated.value = false;
    }
  }

  return { user, isAuthenticated, login, register, logout, fetchCurrentUser };
});
