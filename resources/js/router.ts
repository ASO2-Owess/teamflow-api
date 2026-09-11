import { createRouter, createWebHistory } from "vue-router";
import { getToken } from "@/api";

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: "/", redirect: "/board" },
    { path: "/login", name: "login", component: () => import("@/views/LoginView.vue"), meta: { guestOnly: true } },
    { path: "/board", name: "board", component: () => import("@/views/BoardView.vue"), meta: { requiresAuth: true } },
    { path: "/teams", name: "teams", component: () => import("@/views/TeamsView.vue"), meta: { requiresAuth: true } },
  ],
});

router.beforeEach((to) => {
  const authenticated = !!getToken();

  if (to.meta.requiresAuth && !authenticated) {
    return { name: "login" };
  }
  if (to.meta.guestOnly && authenticated) {
    return { name: "board" };
  }
  return true;
});

export default router;
