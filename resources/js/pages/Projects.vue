<template>
  <AppLayout>
    <div>
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-2xl font-bold" style="color:#f1f5f9;">Projects</h1>
          <p class="text-sm mt-1" style="color:#64748b;">All projects in your workspace</p>
        </div>
        <button @click="showModal = true"
          class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium transition-all hover:opacity-90"
          style="background:linear-gradient(135deg,#6366f1,#8b5cf6);color:white;">
          + New Project
        </button>
      </div>

      <div v-if="loading" class="flex justify-center py-16">
        <div class="w-10 h-10 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <div v-for="project in projects" :key="project.id"
          class="rounded-2xl p-5 cursor-pointer transition-all hover:-translate-y-1 hover:shadow-xl"
          style="background:#1a1a2e;border:1px solid #2d2d4e;" @click="openProject(project)">
          <div class="flex justify-between items-start mb-4">
            <span class="text-xs px-2 py-1 rounded-lg font-medium" :style="statusStyle(project.status)">
              {{ project.status.toUpperCase() }}
            </span>
            <span class="text-xs" style="color:#4a5568;">{{ project.tasks_count }} tasks</span>
          </div>
          <h3 class="font-semibold mb-2" style="color:#f1f5f9;">{{ project.name }}</h3>
          <p class="text-sm mb-4 line-clamp-2" style="color:#4a5568;">
            {{ project.description || 'No description provided.' }}
          </p>
          <div class="flex justify-between items-center pt-3" style="border-top:1px solid #2d2d4e;">
            <span class="text-xs" style="color:#4a5568;">by {{ project.creator?.name || 'Unknown' }}</span>
            <span v-if="project.due_date" class="text-xs" style="color:#64748b;">📅 {{ project.due_date }}</span>
          </div>
        </div>

        <div v-if="!projects.length && !loading"
          class="col-span-3 flex flex-col items-center justify-center py-20 rounded-2xl"
          style="background:#1a1a2e;border:2px dashed #2d2d4e;">
          <span class="text-5xl mb-4">📋</span>
          <p class="font-medium mb-2" style="color:#64748b;">No projects yet</p>
          <p class="text-sm mb-4" style="color:#4a5568;">Create your first project to get started</p>
          <button @click="showModal = true" class="px-4 py-2 rounded-xl text-sm"
            style="background:rgba(99,102,241,0.15);color:#818cf8;">+ Create Project</button>
        </div>
      </div>
    </div>

    <Transition name="fade">
      <div v-if="showModal" class="fixed inset-0 flex items-center justify-center z-50 p-4"
        style="background:rgba(0,0,0,0.7);backdrop-filter:blur(4px);" @click.self="showModal = false">
        <div class="w-full max-w-md rounded-2xl p-6" style="background:#1a1a2e;border:1px solid #2d2d4e;">
          <div class="flex justify-between items-center mb-5">
            <h3 class="text-lg font-bold" style="color:#f1f5f9;">New Project</h3>
            <button @click="showModal = false" style="color:#4a5568;font-size:20px;">×</button>
          </div>
          <div class="space-y-4">
            <div>
              <label class="block text-xs mb-1.5 font-medium uppercase tracking-wide" style="color:#64748b;">Name
                *</label>
              <input v-model="form.name" placeholder="Project name"
                class="w-full px-3 py-2.5 rounded-xl text-sm outline-none"
                style="background:#0f0f1a;border:1px solid #2d2d4e;color:#e2e8f0;" />
            </div>
            <div>
              <label class="block text-xs mb-1.5 font-medium uppercase tracking-wide"
                style="color:#64748b;">Description</label>
              <textarea v-model="form.description" rows="3" placeholder="What is this project about?"
                class="w-full px-3 py-2.5 rounded-xl text-sm outline-none resize-none"
                style="background:#0f0f1a;border:1px solid #2d2d4e;color:#e2e8f0;"></textarea>
            </div>
            <div>
              <label class="block text-xs mb-1.5 font-medium uppercase tracking-wide" style="color:#64748b;">Due
                Date</label>
              <input v-model="form.due_date" type="date" class="w-full px-3 py-2.5 rounded-xl text-sm outline-none"
                style="background:#0f0f1a;border:1px solid #2d2d4e;color:#e2e8f0;" />
            </div>
          </div>
          <div class="flex gap-3 mt-6">
            <button @click="showModal = false" class="flex-1 py-2.5 rounded-xl text-sm"
              style="background:#0f0f1a;border:1px solid #2d2d4e;color:#64748b;">Cancel</button>
            <button @click="createProject" :disabled="!form.name.trim() || creating"
              class="flex-1 py-2.5 rounded-xl text-sm font-medium disabled:opacity-50"
              style="background:linear-gradient(135deg,#6366f1,#8b5cf6);color:white;">
              {{ creating ? 'Creating...' : 'Create' }}
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted, onBeforeMount } from 'vue'
import axios from 'axios'
import AppLayout from '@/components/AppLayout.vue'
import { useAuth } from '@/composables/useAuth'

const { requireAuth, getHeaders } = useAuth()
const WS = 1

const projects = ref([])
const loading = ref(false)
const creating = ref(false)
const showModal = ref(false)
const form = ref({ name: '', description: '', due_date: '' })

onBeforeMount(() => requireAuth())

function statusStyle(status) {
  return {
    active: 'background:rgba(99,102,241,0.15);color:#818cf8;',
    archived: 'background:rgba(100,116,139,0.15);color:#94a3b8;',
    completed: 'background:rgba(34,197,94,0.15);color:#4ade80;',
  }[status] || ''
}

function openProject(project) {
  window.location.href = `/projects/${project.id}`
}

async function loadProjects() {
  loading.value = true
  try {
    const res = await axios.get(`/api/workspaces/${WS}/projects`, { headers: getHeaders() })
    projects.value = res.data.data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

async function createProject() {
  creating.value = true
  try {
    const res = await axios.post(`/api/workspaces/${WS}/projects`, form.value, { headers: getHeaders() })
    projects.value.unshift(res.data.data)
    form.value = { name: '', description: '', due_date: '' }
    showModal.value = false
  } catch (e) {
    console.error(e)
  } finally {
    creating.value = false
  }
}

onMounted(() => loadProjects())
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>