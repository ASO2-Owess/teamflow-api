import { defineStore } from "pinia";
import { ref } from "vue";
import { api } from "@/api";
import type { Task, TaskStatus } from "@/types";

export const useTasksStore = defineStore("tasks", () => {
  const tasks = ref<Task[]>([]);
  const loading = ref(false);

  async function fetchTasks(teamId: number): Promise<void> {
    loading.value = true;
    try {
      const { data } = await api.get<{ data: Task[] }>(`/teams/${teamId}/tasks`);
      tasks.value = data.data;
    } finally {
      loading.value = false;
    }
  }

  async function createTask(teamId: number, title: string, assignedTo?: number | null): Promise<void> {
    const { data } = await api.post<{ data: Task }>(`/teams/${teamId}/tasks`, { title, assigned_to: assignedTo || null });
    tasks.value.unshift(data.data);
  }

  async function updateStatus(task: Task, status: TaskStatus): Promise<void> {
    const { data } = await api.patch<{ data: Task }>(`/tasks/${task.id}`, { status });
    const index = tasks.value.findIndex((t) => t.id === task.id);
    if (index !== -1) tasks.value[index] = data.data;
  }

  function byStatus(status: TaskStatus): Task[] {
    return tasks.value.filter((t) => t.status === status);
  }

  return { tasks, loading, fetchTasks, createTask, updateStatus, byStatus };
});
