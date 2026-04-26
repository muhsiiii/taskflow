<template>
  <AppLayout>
    <div>
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-2xl font-bold" style="color:#f1f5f9;">My Tasks</h1>
          <p class="text-sm mt-1" style="color:#64748b;">All tasks assigned to you</p>
        </div>
        <div class="flex gap-2">
          <button v-for="f in filters" :key="f.value" @click="activeFilter = f.value"
            class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all" :style="activeFilter === f.value
              ? 'background:rgba(99,102,241,0.2);color:#818cf8;border:1px solid rgba(99,102,241,0.3);'
              : 'background:#1a1a2e;color:#64748b;border:1px solid #2d2d4e;'">
            {{ f.label }}
          </button>
        </div>
      </div>

      <div class="grid grid-cols-4 gap-4 mb-8">
        <div v-for="s in taskStats" :key="s.label" class="rounded-xl p-4"
          style="background:#1a1a2e;border:1px solid #2d2d4e;">
          <p class="text-xs uppercase tracking-wide mb-1" style="color:#4a5568;">{{ s.label }}</p>
          <p class="text-2xl font-bold" :style="`color:${s.color}`">{{ s.value }}</p>
        </div>
      </div>

      <div v-if="loading" class="flex justify-center py-16">
        <div class="w-10 h-10 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
      </div>

      <div v-else class="space-y-3">
        <div v-for="task in filteredTasks" :key="task.id"
          class="rounded-xl p-4 flex items-center gap-4 transition-all hover:-translate-x-1"
          style="background:#1a1a2e;border:1px solid #2d2d4e;">
          <div @click="toggleDone(task)"
            class="w-5 h-5 rounded-md border-2 flex items-center justify-center cursor-pointer flex-shrink-0 transition-all"
            :style="task.status === 'done' ? 'background:#22c55e;border-color:#22c55e;' : 'border-color:#2d2d4e;'">
            <span v-if="task.status === 'done'" class="text-white text-xs">✓</span>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium truncate"
              :style="task.status === 'done' ? 'color:#4a5568;text-decoration:line-through;' : 'color:#e2e8f0;'">
              {{ task.title }}
            </p>
            <p v-if="task.description" class="text-xs mt-0.5 truncate" style="color:#4a5568;">{{ task.description }}</p>
          </div>
          <span class="text-xs px-2 py-0.5 rounded-md font-medium flex-shrink-0" :style="priorityStyle(task.priority)">
            {{ task.priority }}
          </span>
          <span class="text-xs px-2 py-0.5 rounded-md flex-shrink-0" :style="statusBadge(task.status)">
            {{ task.status.replace('_', ' ') }}
          </span>
          <span v-if="task.due_date" class="text-xs flex-shrink-0"
            :style="isOverdue(task) ? 'color:#ef4444;' : 'color:#4a5568;'">
            📅 {{ task.due_date }}
          </span>
          <span v-if="task.subtasks_count" class="text-xs" style="color:#4a5568;">🔗 {{ task.subtasks_count }}</span>
        </div>

        <div v-if="!filteredTasks.length && !loading"
          class="flex flex-col items-center justify-center py-20 rounded-2xl"
          style="background:#1a1a2e;border:2px dashed #2d2d4e;">
          <span class="text-5xl mb-4">✅</span>
          <p class="font-medium" style="color:#64748b;">No tasks found</p>
          <p class="text-sm mt-1" style="color:#4a5568;">Go to Dashboard to create tasks</p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeMount } from 'vue'
import axios from 'axios'
import AppLayout from '@/components/AppLayout.vue'
import { useAuth } from '@/composables/useAuth'

const { requireAuth, getHeaders } = useAuth()
const WS = 1
const PR = 1

const tasks = ref([])
const loading = ref(false)
const activeFilter = ref('all')

const filters = [
  { value: 'all', label: 'All' },
  { value: 'todo', label: 'To Do' },
  { value: 'in_progress', label: 'In Progress' },
  { value: 'done', label: 'Done' },
]

onBeforeMount(() => requireAuth())

const filteredTasks = computed(() =>
  activeFilter.value === 'all' ? tasks.value : tasks.value.filter(t => t.status === activeFilter.value)
)

const taskStats = computed(() => [
  { label: 'Total', value: tasks.value.length, color: '#6366f1' },
  { label: 'To Do', value: tasks.value.filter(t => t.status === 'todo').length, color: '#64748b' },
  { label: 'In Progress', value: tasks.value.filter(t => t.status === 'in_progress').length, color: '#f59e0b' },
  { label: 'Done', value: tasks.value.filter(t => t.status === 'done').length, color: '#22c55e' },
])

function isOverdue(task) {
  if (!task.due_date || task.status === 'done') return false
  return task.due_date < new Date().toISOString().split('T')[0]
}

function priorityStyle(p) {
  return { low: 'background:rgba(100,116,139,0.15);color:#94a3b8;', medium: 'background:rgba(99,102,241,0.15);color:#818cf8;', high: 'background:rgba(245,158,11,0.15);color:#fbbf24;', urgent: 'background:rgba(239,68,68,0.15);color:#f87171;' }[p] || ''
}

function statusBadge(s) {
  return { todo: 'background:rgba(100,116,139,0.15);color:#94a3b8;', in_progress: 'background:rgba(99,102,241,0.15);color:#818cf8;', in_review: 'background:rgba(245,158,11,0.15);color:#fbbf24;', done: 'background:rgba(34,197,94,0.15);color:#4ade80;' }[s] || ''
}

async function toggleDone(task) {
  const newStatus = task.status === 'done' ? 'todo' : 'done'
  task.status = newStatus
  await axios.patch(`/api/workspaces/${WS}/projects/${PR}/tasks/${task.id}/move`, { status: newStatus }, { headers: getHeaders() })
}

async function loadTasks() {
  loading.value = true
  try {
    const res = await axios.get(`/api/workspaces/${WS}/projects/${PR}/tasks`, { headers: getHeaders() })
    tasks.value = res.data.data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

onMounted(() => loadTasks())
</script>