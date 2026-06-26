<template>
  <AppLayout>
    <div class="space-y-8">
      <div class="flex flex-col gap-4 lg:flex-row lg:justify-between lg:items-end">
        <div>
          <p class="text-sm uppercase tracking-[0.35em] text-indigo-400">Project details</p>
          <h1 class="text-3xl font-semibold" style="color:#f1f5f9;">{{ project.name }}</h1>
          <p class="text-slate-400 mt-2">{{ project.description || 'This project has no description yet.' }}</p>
        </div>
        <div class="flex flex-wrap gap-3">
          <span class="rounded-full px-3 py-2 text-sm" :style="statusStyle(project.status)">{{ project.status }}</span>
          <span class="rounded-full px-3 py-2 text-sm bg-slate-900/90 text-slate-300">{{ project.tasks_count }} tasks</span>
          <span v-if="project.due_date" class="rounded-full px-3 py-2 text-sm bg-slate-900/90 text-slate-300">Due {{ project.due_date }}</span>
        </div>
      </div>

      <div class="grid gap-6 lg:grid-cols-[0.65fr_0.35fr]">
        <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-6">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h2 class="text-xl font-semibold text-white">Project board</h2>
              <p class="text-slate-400 text-sm">Tasks grouped by status for this project.</p>
            </div>
            <button @click="newTask = true" class="rounded-full bg-indigo-500 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-400">+ Add task</button>
          </div>
          <div class="space-y-4">
            <div v-for="col in columns" :key="col.status" class="rounded-2xl p-4" style="background:#11121d;border:1px solid #2d2d4e;">
              <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                  <div class="h-2.5 w-2.5 rounded-full" :style="`background:${col.color}`"></div>
                  <h3 class="text-sm font-semibold text-white">{{ col.label }}</h3>
                </div>
                <span class="text-xs text-slate-400">{{ tasksByStatus[col.status].length }}</span>
              </div>
              <div class="space-y-3">
                <div v-for="task in tasksByStatus[col.status]" :key="task.id" class="rounded-2xl p-4 bg-slate-950 border border-slate-800">
                  <div class="flex items-start justify-between gap-3">
                    <div>
                      <p class="font-semibold text-white">{{ task.title }}</p>
                      <p class="text-xs text-slate-400 mt-1">{{ task.description || 'No description.' }}</p>
                    </div>
                    <span class="text-xs rounded-full px-2 py-1" :style="priorityStyle(task.priority || 'medium')">{{ task.priority || 'medium' }}</span>
                  </div>
                  <div class="mt-3 flex flex-wrap gap-2 text-xs text-slate-400">
                    <span v-if="task.assignee">Assigned to {{ task.assignee.name }}</span>
                    <span v-if="task.due_date">Due {{ task.due_date }}</span>
                    <span v-if="task.subtasks_count">{{ task.subtasks_count }} subtasks</span>
                    <span v-if="task.attachments_count">{{ task.attachments_count }} attachments</span>
                  </div>
                </div>
              </div>
              <div v-if="!tasksByStatus[col.status].length" class="rounded-2xl border border-dashed border-slate-700 p-6 text-center text-sm text-slate-500">No tasks here yet.</div>
            </div>
          </div>
        </div>

        <div class="space-y-6">
          <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-6">
            <h3 class="text-xl font-semibold text-white">Project summary</h3>
            <div class="mt-4 space-y-3 text-sm text-slate-400">
              <div><span class="font-semibold text-slate-200">Workspace:</span> {{ project.workspace?.name || 'Workspace' }}</div>
              <div><span class="font-semibold text-slate-200">Created by:</span> {{ project.creator?.name || 'Creator' }}</div>
              <div><span class="font-semibold text-slate-200">Status:</span> {{ project.status || 'Unknown' }}</div>
              <div><span class="font-semibold text-slate-200">Members:</span> {{ project.members?.length || 0 }}</div>
            </div>
          </div>

          <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-6">
            <h3 class="text-xl font-semibold text-white">Project progress</h3>
            <div class="mt-4">
              <div class="flex items-center justify-between mb-3 text-sm text-slate-400">
                <span>{{ completionPct }}% complete</span>
                <span>{{ tasks.value.length || 0 }} tasks</span>
              </div>
              <div class="h-2 rounded-full overflow-hidden bg-slate-800">
                <div class="h-full rounded-full transition-all duration-700" :style="`width:${completionPct}%;background:linear-gradient(90deg,#6366f1,#8b5cf6);`"></div>
              </div>
              <div class="grid grid-cols-2 gap-2 mt-4 text-xs text-slate-400">
                <div>To do: {{ statusCounts.todo }}</div>
                <div>In progress: {{ statusCounts.in_progress }}</div>
                <div>In review: {{ statusCounts.in_review }}</div>
                <div>Done: {{ statusCounts.done }}</div>
              </div>
            </div>
          </div>

          <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-6">
            <h3 class="text-xl font-semibold text-white">Upcoming timeline</h3>
            <p class="mt-3 text-slate-400 text-sm">Tasks due soon help keep the project on track.</p>
            <div class="mt-5 space-y-3 text-sm text-slate-400">
              <div v-if="!soonTasks.length" class="text-slate-500">No upcoming due dates yet.</div>
              <div v-for="task in soonTasks" :key="task.id" class="rounded-2xl border border-slate-800 bg-slate-950 p-3">
                <div class="flex items-center justify-between gap-2">
                  <p class="font-medium text-white truncate">{{ task.title }}</p>
                  <span class="text-xs rounded-full px-2 py-1 bg-slate-800 text-slate-400">{{ task.due_date ? formatDate(task.due_date) : 'No date' }}</span>
                </div>
                <p class="text-xs text-slate-500 mt-1">{{ task.status ? task.status.replace('_', ' ') : 'Unknown' }}</p>
              </div>
            </div>
          </div>

          <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-6">
            <h3 class="text-xl font-semibold text-white">Project members</h3>
            <div class="mt-4 space-y-3 text-sm text-slate-400">
              <div v-if="!(project.members || []).length" class="text-slate-500">No project members added yet.</div>
              <div v-for="member in project.members || []" :key="member.id" class="rounded-2xl bg-slate-950 p-3 border border-slate-800">
                <div class="flex items-center justify-between gap-3">
                  <div>
                    <p class="text-sm font-semibold text-white">{{ member.name }}</p>
                    <p class="text-xs text-slate-500">{{ member.role }}</p>
                  </div>
                  <button @click="removeProjectMember(member.id)"
                    class="rounded-full px-3 py-1 text-xs text-slate-300 bg-slate-800 hover:bg-slate-700">Remove</button>
                </div>
              </div>
            </div>
            <div class="mt-5 pt-5 border-t border-slate-800">
              <h4 class="text-sm font-medium text-white">Add member</h4>
              <p class="text-xs text-slate-500 mb-3">Invite an existing workspace member by email.</p>
              <div class="space-y-3">
                <input v-model="memberForm.email" placeholder="Member email" type="email"
                  class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white outline-none" />
                <select v-model="memberForm.role"
                  class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white outline-none">
                  <option value="contributor">Contributor</option>
                  <option value="manager">Manager</option>
                  <option value="owner">Owner</option>
                </select>
                <div class="flex gap-3">
                  <button @click="addProjectMember"
                    class="flex-1 rounded-2xl bg-indigo-500 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-400">Add member</button>
                  <button @click="resetMemberForm"
                    class="flex-1 rounded-2xl border border-slate-700 px-4 py-3 text-sm text-slate-200">Clear</button>
                </div>
                <p v-if="memberError" class="text-xs text-rose-400">{{ memberError }}</p>
              </div>
            </div>
          </div>

          <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-6">
            <h3 class="text-xl font-semibold text-white">Task templates</h3>
            <p class="mt-3 text-slate-400 text-sm">Reuse common task patterns and launch work faster.</p>
            <div class="mt-5 space-y-3">
              <div v-if="!(project.task_templates || []).length" class="text-slate-500">No templates are available yet.</div>
              <div v-for="template in project.task_templates || []" :key="template.id"
                class="rounded-2xl border border-slate-800 bg-slate-950 p-4">
                <div class="flex items-start justify-between gap-3">
                  <div>
                    <p class="font-semibold text-white">{{ template.name }}</p>
                    <p class="text-xs text-slate-500 mt-1">{{ template.description || 'No description available.' }}</p>
                  </div>
                  <button @click="applyTemplate(template)"
                    class="rounded-full bg-indigo-500 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-400">Use template</button>
                </div>
                <div class="mt-3 flex flex-wrap gap-2 text-xs text-slate-400">
                  <span class="rounded-full bg-slate-800 px-2 py-1">Status: {{ template.status.replace('_', ' ') }}</span>
                  <span v-if="template.estimate_minutes" class="rounded-full bg-slate-800 px-2 py-1">Estimate: {{ template.estimate_minutes }} min</span>
                </div>
              </div>
            </div>
            <p v-if="templateError" class="mt-3 text-xs text-rose-400">{{ templateError }}</p>
          </div>
        </div>
      </div>
    </div>

    <Transition name="fade">
      <div v-if="newTask" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4" @click.self="newTask = false">
        <div class="w-full max-w-lg rounded-3xl bg-slate-900 p-6 border border-white/10">
          <div class="flex items-center justify-between mb-5">
            <h3 class="text-xl font-semibold text-white">Add task to project</h3>
            <button @click="newTask = false" class="text-slate-400 text-2xl leading-none">×</button>
          </div>
          <div class="space-y-4">
            <div>
              <label class="block text-xs uppercase tracking-[0.2em] text-slate-400">Task title</label>
              <input v-model="taskForm.title" class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white outline-none" />
            </div>
            <div>
              <label class="block text-xs uppercase tracking-[0.2em] text-slate-400">Description</label>
              <textarea v-model="taskForm.description" rows="3" class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white outline-none"></textarea>
            </div>
            <div class="grid gap-3 sm:grid-cols-2">
              <div>
                <label class="block text-xs uppercase tracking-[0.2em] text-slate-400">Priority</label>
                <select v-model="taskForm.priority" class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white outline-none">
                  <option value="low">Low</option>
                  <option value="medium">Medium</option>
                  <option value="high">High</option>
                  <option value="urgent">Urgent</option>
                </select>
              </div>
              <div>
                <label class="block text-xs uppercase tracking-[0.2em] text-slate-400">Due date</label>
                <input v-model="taskForm.due_date" type="date" class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white outline-none" />
              </div>
            </div>
          </div>
          <div class="mt-6 flex gap-3">
            <button @click="newTask = false" class="flex-1 rounded-2xl border border-slate-700 px-4 py-3 text-sm text-slate-300">Cancel</button>
            <button @click="createProjectTask" class="flex-1 rounded-2xl bg-indigo-500 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-400">Create task</button>
          </div>
        </div>
      </div>
    </Transition>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeMount } from 'vue'
import axios from 'axios'
import AppLayout from '@/components/AppLayout.vue'
import { useAuth } from '@/composables/useAuth'

const { requireAuth, getHeaders } = useAuth()
const WS = 1

const project = ref({
  name: '',
  description: '',
  status: '',
  due_date: null,
  workspace: {},
  creator: {},
  tasks_count: 0,
  members: [],
  task_templates: [],
})
const tasks = ref([])
const newTask = ref(false)
const taskForm = ref({ title: '', description: '', priority: 'medium', due_date: '' })
const memberForm = ref({ email: '', role: 'contributor' })
const memberError = ref('')
const templateError = ref('')

const columns = [
  { status: 'todo', label: 'To Do', color: '#64748b' },
  { status: 'in_progress', label: 'In Progress', color: '#6366f1' },
  { status: 'in_review', label: 'In Review', color: '#f59e0b' },
  { status: 'done', label: 'Done', color: '#22c55e' },
]

const tasksByStatus = computed(() => ({
  todo: tasks.value.filter(t => t.status === 'todo'),
  in_progress: tasks.value.filter(t => t.status === 'in_progress'),
  in_review: tasks.value.filter(t => t.status === 'in_review'),
  done: tasks.value.filter(t => t.status === 'done'),
}))

const statusCounts = computed(() => ({
  todo: tasksByStatus.value.todo.length,
  in_progress: tasksByStatus.value.in_progress.length,
  in_review: tasksByStatus.value.in_review.length,
  done: tasksByStatus.value.done.length,
}))

const completionPct = computed(() => {
  if (!tasks.value.length) return 0
  return Math.round((statusCounts.value.done / tasks.value.length) * 100)
})

const soonTasks = computed(() => {
  const today = new Date().toISOString().split('T')[0]
  return tasks.value
    .filter(t => t.due_date && t.due_date >= today)
    .sort((a, b) => a.due_date.localeCompare(b.due_date))
    .slice(0, 5)
})

function formatDate(date) {
  return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
}

onBeforeMount(() => requireAuth())
onMounted(() => loadProject())

function statusStyle(status) {
  return {
    active: 'background:rgba(99,102,241,0.15);color:#818cf8;',
    archived: 'background:rgba(100,116,139,0.15);color:#94a3b8;',
    completed: 'background:rgba(34,197,94,0.15);color:#4ade80;',
  }[status] || 'background:rgba(55,65,81,0.15);color:#cbd5e1;'
}

function priorityStyle(priority) {
  return {
    low: 'background:rgba(100,116,139,0.15);color:#94a3b8;',
    medium: 'background:rgba(99,102,241,0.15);color:#818cf8;',
    high: 'background:rgba(245,158,11,0.15);color:#fbbf24;',
    urgent: 'background:rgba(239,68,68,0.15);color:#f87171;',
  }[priority] || 'background:rgba(100,116,139,0.15);color:#94a3b8;'
}

function getProjectId() {
  return window.location.pathname.split('/').pop()
}

async function loadProject() {
  try {
    const res = await axios.get(`/api/workspaces/${WS}/projects/${getProjectId()}`, { headers: getHeaders() })
    project.value = res.data.data
    tasks.value = res.data.data.rootTasks || []
  } catch (e) {
    console.error('Failed to load project', e)
    window.location.href = '/projects'
  }
}

async function addProjectMember() {
  memberError.value = ''

  if (!memberForm.value.email.trim()) {
    memberError.value = 'Please enter a valid email.'
    return
  }

  try {
    const res = await axios.post(
      `/api/workspaces/${WS}/projects/${getProjectId()}/members`,
      memberForm.value,
      { headers: getHeaders() }
    )

    project.value.members.push(res.data.data)
    memberForm.value = { email: '', role: 'contributor' }
  } catch (e) {
    memberError.value = e.response?.data?.message || 'Failed to add member.'
  }
}

async function removeProjectMember(memberId) {
  try {
    await axios.delete(
      `/api/workspaces/${WS}/projects/${getProjectId()}/members/${memberId}`,
      { headers: getHeaders() }
    )

    project.value.members = project.value.members.filter(m => m.id !== memberId)
  } catch (e) {
    console.error('Failed to remove member', e)
  }
}

function resetMemberForm() {
  memberForm.value = { email: '', role: 'contributor' }
  memberError.value = ''
}

async function applyTemplate(template) {
  templateError.value = ''
  try {
    await axios.post(
      `/api/workspaces/${WS}/projects/${getProjectId()}/tasks`,
      {
        title: template.name,
        description: template.description,
        status: template.status,
        priority: 'medium',
      },
      { headers: getHeaders() }
    )
    await loadProject()
  } catch (e) {
    templateError.value = e.response?.data?.message || 'Failed to apply template.'
  }
}

async function createProjectTask() {
  if (!taskForm.value.title.trim()) return
  try {
    await axios.post(`/api/workspaces/${WS}/projects/${getProjectId()}/tasks`, taskForm.value, { headers: getHeaders() })
    taskForm.value = { title: '', description: '', priority: 'medium', due_date: '' }
    newTask.value = false
    await loadProject()
  } catch (e) {
    console.error('Failed to create task', e)
  }
}
</script>
