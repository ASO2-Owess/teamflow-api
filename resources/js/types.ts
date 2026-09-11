export interface User {
  id: number;
  name: string;
  email: string;
  role?: "admin" | "manager" | "member" | null;
}

export interface Team {
  id: number;
  name: string;
  slug: string;
  owner?: User;
  members?: User[];
  members_count?: number;
  created_at: string;
}

export type TaskStatus = "todo" | "in_progress" | "done";
export type TaskPriority = "low" | "medium" | "high";

export interface Task {
  id: number;
  team_id: number;
  title: string;
  description: string | null;
  status: TaskStatus;
  priority: TaskPriority;
  due_date: string | null;
  assignee?: User | null;
  created_by?: User;
  created_at: string;
  updated_at: string;
}

export interface Paginated<T> {
  data: T[];
  meta?: Record<string, unknown>;
}
