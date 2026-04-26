<template>
  <div class="min-h-screen flex" style="background:#0f0f1a;color:#e2e8f0;font-family:'Inter',system-ui,sans-serif;">
    <div class="fixed left-0 top-0 h-full w-16 flex flex-col items-center py-6 gap-4 z-50"
      style="background:#1a1a2e;border-right:1px solid #2d2d4e;">
      <div class="w-9 h-9 rounded-xl flex items-center justify-center text-lg font-bold mb-2"
        style="background:linear-gradient(135deg,#6366f1,#8b5cf6);">⚡</div>
      <div class="w-px h-4" style="background:#2d2d4e;"></div>
      <div v-for="nav in navItems" :key="nav.href"
        class="w-10 h-10 rounded-xl flex items-center justify-center cursor-pointer transition-all hover:scale-110 relative group"
        :style="isActive(nav.href) ? 'background:rgba(99,102,241,0.2);' : 'color:#4a5568;'" @click="go(nav.href)">
        <span class="text-xl">{{ nav.icon }}</span>
        <div v-if="isActive(nav.href)" class="absolute right-0 w-0.5 h-6 rounded-l-full" style="background:#6366f1;">
        </div>
        <div
          class="absolute left-14 px-2 py-1 rounded-lg text-xs whitespace-nowrap opacity-0 group-hover:opacity-100 pointer-events-none transition-all z-50"
          style="background:#2d2d4e;color:#e2e8f0;">{{ nav.label }}</div>
      </div>
      <div class="flex-1"></div>
      <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm font-bold mb-1"
        style="background:linear-gradient(135deg,#6366f1,#8b5cf6);color:white;" :title="user?.name">
        {{ userInitial }}
      </div>
      <div
        class="w-10 h-10 rounded-xl flex items-center justify-center cursor-pointer hover:scale-110 transition-all group relative"
        title="Logout" @click="handleLogout">
        <span class="text-xl" style="color:#ef4444;">🚪</span>
        <div
          class="absolute left-14 px-2 py-1 rounded-lg text-xs whitespace-nowrap opacity-0 group-hover:opacity-100 pointer-events-none z-50"
          style="background:#2d2d4e;color:#f87171;">Logout</div>
      </div>
    </div>
    <div class="ml-16 flex-1 p-8 overflow-auto">
      <slot />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useAuth } from '@/composables/useAuth'

const { user, logout } = useAuth()

const navItems = [
  { icon: '🏠', label: 'Dashboard', href: '/' },
  { icon: '📋', label: 'Projects', href: '/projects' },
  { icon: '✅', label: 'My Tasks', href: '/tasks' },
]

const userInitial = computed(() => user?.name ? user.name.charAt(0).toUpperCase() : '?')

function isActive(href) { return window.location.pathname === href }
function go(href) { window.location.href = href }

async function handleLogout() {
  if (confirm('Are you sure you want to logout?')) {
    await logout()
  }
}
</script>