<template>
  <div class="min-h-screen" style="background: #0f0f1a; color: #e2e8f0; font-family: 'Inter', system-ui, sans-serif;">

    <!-- Sidebar -->
    <div class="fixed left-0 top-0 h-full w-16 flex flex-col items-center py-6 gap-6 z-50"
         style="background: #1a1a2e; border-right: 1px solid #2d2d4e;">
      <div class="w-9 h-9 rounded-xl flex items-center justify-center text-lg font-bold"
           style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">⚡</div>
      <div class="w-px flex-1" style="background: #2d2d4e;"></div>
      <div v-for="nav in navItems" :key="nav.icon"
           class="w-10 h-10 rounded-xl flex items-center justify-center cursor-pointer transition-all hover:scale-110"
           :style="nav.active ? 'background: rgba(99,102,241,0.2); color: #6366f1;' : 'color: #4a5568;'"
           :title="nav.label">
        <span class="text-lg">{{ nav.icon }}</span>
      </div>
    </div>

    <!-- Main content -->
    <div class="ml-16 p-8">

      <!-- Top bar -->
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-2xl font-bold" style="color: #f1f5f9;">TaskFlow
            <span class="text-sm font-normal ml-2 px-2 py-0.5 rounded-full"
                  style="background: rgba(99,102,241,0.15); color: #818cf8;">v1.0.0</span>
          </h1>
          <p style="color: #64748b; font-size: 14px;">My First Workspace · TaskFlow App</p>
        </div>
        <div class="flex items-center gap-3">
          <div class="px-3 py-1.5 rounded-lg text-sm" style="background: #1e1e3a; color: #64748b;">
            {{ new Date().toLocaleDateString('en-US', {weekday:'short', month:'short', day:'numeric'}) }}
          </div>
          <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-sm"
               style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">J</div>
        </div>
      </div>

      <!-- Stats row -->
      <div class="grid grid-cols-4 gap-4 mb-8">
        <div v-for="stat in stats" :key="stat.label"
             class="rounded-2xl p-5 relative overflow-hidden"
             style="background: #1a1a2e; border: 1px solid #2d2d4e;">
          <div class="absolute top-0 right-0 w-20 h-20 rounded-full opacity-10"
               :style="`background: ${stat.color}; transform: translate(30%, -30%);`"></div>
          <p class="text-xs uppercase tracking-widest mb-2" style="color: #4a5568;">{{ stat.label }}</p>
          <p class="text-4xl font-bold mb-1" :style="`color: ${stat.color}`">{{ stat.value }}</p>
          <p class="text-xs" style="color: #4a5568;">{{ stat.sub }}</p>
        </div>
      </div>

      <!-- Progress bar -->
      <div class="rounded-2xl p-5 mb-8" style="background: #1a1a2e; border: 1px solid #2d2d4e;">
        <div class="flex justify-between items-center mb-3">
          <span class="text-sm font-medium" style="color: #94a3b8;">Overall Progress</span>
          <span class="text-sm font-bold" style="color: #6366f1;">{{ completionPct }}%</span>
        </div>
        <div class="h-2 rounded-full overflow-hidden" style="background: #2d2d4e;">
          <div class="h-full rounded-full transition-all duration-700"
               :style="`width: ${completionPct}%; background: linear-gradient(90deg, #6366f1, #8b5cf6);`"></div>
        </div>
        <div class="flex justify-between mt-2" style="color: #4a5568; font-size: 12px;">
          <span>{{ byStatus('done').length }} completed</span>
          <span>{{ tasks.length }} total tasks</span>
        </div>
      </div>

      <!-- Kanban header -->
      <div class="flex justify-between items-center mb-5">
        <div class="flex items-center gap-3">
          <h2 class="text-lg font-semibold" style="color: #f1f5f9;">Kanban Board</h2>
          <div v-if="loading" class="w-4 h-4 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
        </div>
        <button @click="showModal = true"
                class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium transition-all hover:scale-105"
                style="background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white;">
          <span>+</span> New Task
        </button>
      </div>

      <!-- Kanban columns -->
      <div class="grid grid-cols-3 gap-5">
        <div v-for="col in columns" :key="col.status"
             class="rounded-2xl p-4 min-h-96 transition-all"
             :style="`background: #1a1a2e; border: 2px solid ${dragOver === col.status ? col.color : '#2d2d4e'};`"
             @dragover.prevent="dragOver = col.status"
             @dragleave="dragOver = null"
             @drop="onDrop($event, col.status)">

          <!-- Column header -->
          <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-2">
              <div class="w-2.5 h-2.5 rounded-full" :style="`background: ${col.color};`"></div>
              <span class="font-semibold text-sm" style="color: #e2e8f0;">{{ col.label }}</span>
            </div>
            <span class="text-xs px-2 py-0.5 rounded-full font-medium"
                  :style="`background: ${col.color}20; color: ${col.color};`">
              {{ byStatus(col.status).length }}
            </span>
          </div>

          <!-- Task cards -->
          <div class="space-y-3">
            <div v-for="task in byStatus(col.status)" :key="task.id"
                 class="rounded-xl p-4 cursor-grab group transition-all hover:-translate-y-0.5"
                 style="background: #0f0f1a; border: 1px solid #2d2d4e;"
                 draggable="true"
                 @dragstart="dragTask = task.id"
                 @dragend="dragOver = null">

              <!-- Priority + assignee -->
              <div class="flex justify-between items-center mb-3">
                <span class="text-xs px-2 py-0.5 rounded-md font-medium" :style="priorityStyle(task.priority)">
                  {{ task.priority.toUpperCase() }}
                </span>
                <div v-if="task.assignee"
                     class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold"
                     style="background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white;"
                     :title="task.assignee.name">
                  {{ task.assignee.name.charAt(0).toUpperCase() }}
                </div>
              </div>

              <!-- Title -->
              <p class="text-sm font-medium mb-3 leading-snug" style="color: #e2e8f0;">{{ task.title }}</p>

              <!-- Description -->
              <p v-if="task.description" class="text-xs mb-3 line-clamp-2" style="color: #4a5568;">{{ task.description }}</p>

              <!-- Footer -->
              <div class="flex justify-between items-center pt-2" style="border-top: 1px solid #2d2d4e;">
                <span v-if="task.due_date" class="text-xs flex items-center gap-1"
                      :style="isOverdue(task) ? 'color: #ef4444;' : 'color: #4a5568;'">
                  📅 {{ task.due_date }}
                </span>
                <span v-else class="text-xs" style="color: #2d2d4e;">No due date</span>
                <div class="flex gap-2">
                  <span v-if="task.subtasks_count" class="text-xs" style="color: #4a5568;">🔗 {{ task.subtasks_count }}</span>
                  <span v-if="task.attachments_count" class="text-xs" style="color: #4a5568;">📎 {{ task.attachments_count }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty state -->
          <div v-if="!byStatus(col.status).length"
               class="flex flex-col items-center justify-center h-32 mt-2 rounded-xl border-2 border-dashed"
               :style="`border-color: ${col.color}30;`">
            <span class="text-2xl mb-1">{{ col.emoji }}</span>
            <p class="text-xs" style="color: #4a5568;">Drop tasks here</p>
          </div>
        </div>
      </div>
    </div>

    <!-- New Task Modal -->
    <Transition name="fade">
      <div v-if="showModal"
           class="fixed inset-0 flex items-center justify-center z-50 p-4"
           style="background: rgba(0,0,0,0.7); backdrop-filter: blur(4px);"
           @click.self="showModal = false">
        <div class="w-full max-w-md rounded-2xl p-6 shadow-2xl"
             style="background: #1a1a2e; border: 1px solid #2d2d4e;">

          <div class="flex justify-between items-center mb-5">
            <h3 class="text-lg font-bold" style="color: #f1f5f9;">Create New Task</h3>
            <button @click="showModal = false" style="color: #4a5568; font-size: 20px; line-height:1;">×</button>
          </div>

          <div class="space-y-4">
            <div>
              <label class="block text-xs mb-1.5 font-medium" style="color: #64748b;">TITLE *</label>
              <input v-model="form.title" placeholder="What needs to be done?"
                     class="w-full px-3 py-2.5 rounded-xl text-sm outline-none transition-all"
                     style="background: #0f0f1a; border: 1px solid #2d2d4e; color: #e2e8f0;"
                     @focus="$event.target.style.borderColor='#6366f1'"
                     @blur="$event.target.style.borderColor='#2d2d4e'" />
            </div>

            <div>
              <label class="block text-xs mb-1.5 font-medium" style="color: #64748b;">DESCRIPTION</label>
              <textarea v-model="form.description" placeholder="Add more details..." rows="2"
                        class="w-full px-3 py-2.5 rounded-xl text-sm outline-none resize-none"
                        style="background: #0f0f1a; border: 1px solid #2d2d4e; color: #e2e8f0;"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs mb-1.5 font-medium" style="color: #64748b;">PRIORITY</label>
                <select v-model="form.priority"
                        class="w-full px-3 py-2.5 rounded-xl text-sm outline-none"
                        style="background: #0f0f1a; border: 1px solid #2d2d4e; color: #e2e8f0;">
                  <option value="low">🟢 Low</option>
                  <option value="medium">🔵 Medium</option>
                  <option value="high">🟠 High</option>
                  <option value="urgent">🔴 Urgent</option>
                </select>
              </div>
              <div>
                <label class="block text-xs mb-1.5 font-medium" style="color: #64748b;">DUE DATE</label>
                <input v-model="form.due_date" type="date"
                       class="w-full px-3 py-2.5 rounded-xl text-sm outline-none"
                       style="background: #0f0f1a; border: 1px solid #2d2d4e; color: #e2e8f0;" />
              </div>
            </div>
          </div>

          <div class="flex gap-3 mt-6">
            <button @click="showModal = false"
                    class="flex-1 py-2.5 rounded-xl text-sm transition-all"
                    style="background: #0f0f1a; border: 1px solid #2d2d4e; color: #64748b;">
              Cancel
            </button>
            <button @click="createTask" :disabled="!form.title.trim() || creating"
                    class="flex-1 py-2.5 rounded-xl text-sm font-medium transition-all hover:opacity-90 disabled:opacity-50"
                    style="background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white;">
              {{ creating ? 'Creating...' : 'Create Task' }}
            </button>
          </div>
        </div>
      </div>
    </Transition>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const WS    = 1
const PR    = 1
const TOKEN = '2|3LlJMvOb4r9EdHhWGIGKHe5b79IozsvwFcMstpftdada7524'
const headers = { Authorization: `Bearer ${TOKEN}` }

const tasks    = ref([])
const loading  = ref(false)
const creating = ref(false)
const showModal = ref(false)
const dragTask  = ref(null)
const dragOver  = ref(null)

const form = ref({ title: '', description: '', priority: 'medium', due_date: '' })

const navItems = [
  { icon: '🏠', label: 'Dashboard', active: true },
  { icon: '📋', label: 'Projects',  active: false },
  { icon: '✅', label: 'My Tasks',  active: false },
  { icon: '📊', label: 'Reports',   active: false },
  { icon: '⚙️', label: 'Settings',  active: false },
]

const columns = [
  { status: 'todo',        label: 'To Do',      color: '#64748b', emoji: '📝' },
  { status: 'in_progress', label: 'In Progress', color: '#6366f1', emoji: '⚡' },
  { status: 'done',        label: 'Done',        color: '#22c55e', emoji: '🎉' },
]

const completionPct = computed(() => {
  if (!tasks.value.length) return 0
  return Math.round((byStatus('done').length / tasks.value.length) * 100)
})

const stats = computed(() => [
  { label: 'Total Tasks',  value: tasks.value.length,          color: '#6366f1', sub: 'across all columns' },
  { label: 'Completed',    value: byStatus('done').length,     color: '#22c55e', sub: `${completionPct.value}% done` },
  { label: 'In Progress',  value: byStatus('in_progress').length, color: '#f59e0b', sub: 'being worked on' },
  { label: 'To Do',        value: byStatus('todo').length,     color: '#64748b', sub: 'not started yet' },
])

function byStatus(status) {
  return tasks.value.filter(t => t.status === status)
}

function isOverdue(task) {
  if (!task.due_date || task.status === 'done') return false
  return task.due_date < new Date().toISOString().split('T')[0]
}

function priorityStyle(priority) {
  const map = {
    low:    'background: rgba(100,116,139,0.15); color: #94a3b8;',
    medium: 'background: rgba(99,102,241,0.15); color: #818cf8;',
    high:   'background: rgba(245,158,11,0.15); color: #fbbf24;',
    urgent: 'background: rgba(239,68,68,0.15); color: #f87171;',
  }
  return map[priority] || map.low
}

async function loadTasks() {
  loading.value = true
  try {
    const res = await axios.get(`/api/workspaces/${WS}/projects/${PR}/tasks`, { headers })
    tasks.value = res.data.data
  } catch (e) {
    console.error('Failed:', e)
  } finally {
    loading.value = false
  }
}

async function createTask() {
  if (!form.value.title.trim()) return
  creating.value = true
  try {
    const res = await axios.post(`/api/workspaces/${WS}/projects/${PR}/tasks`, form.value, { headers })
    tasks.value.push(res.data.data)
    form.value = { title: '', description: '', priority: 'medium', due_date: '' }
    showModal.value = false
  } catch(e) {
    console.error(e)
  } finally {
    creating.value = false
  }
}

async function onDrop(event, newStatus) {
  dragOver.value = null
  if (!dragTask.value) return
  const task = tasks.value.find(t => t.id === dragTask.value)
  if (task && task.status !== newStatus) {
    task.status = newStatus
    try {
      await axios.patch(
        `/api/workspaces/${WS}/projects/${PR}/tasks/${dragTask.value}/move`,
        { status: newStatus }, { headers }
      )
    } catch(e) { console.error(e) }
  }
  dragTask.value = null
}

onMounted(() => loadTasks())
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>