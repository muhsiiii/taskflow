<template>
  <div class="min-h-screen flex items-center justify-center p-4"
    style="background:#0f0f1a;font-family:'Inter',system-ui,sans-serif;">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute top-1/4 left-1/2 -translate-x-1/2 w-96 h-96 rounded-full opacity-10"
        style="background:radial-gradient(circle,#6366f1,transparent);filter:blur(60px);"></div>
    </div>
    <div class="w-full max-w-sm relative z-10">
      <div class="text-center mb-8">
        <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4"
          style="background:linear-gradient(135deg,#6366f1,#8b5cf6);">⚡</div>
        <h1 class="text-2xl font-bold" style="color:#f1f5f9;">TaskFlow</h1>
        <p class="text-sm mt-1" style="color:#64748b;">Sign in to your workspace</p>
      </div>
      <div class="rounded-2xl p-6" style="background:#1a1a2e;border:1px solid #2d2d4e;">
        <div v-if="error" class="mb-4 px-3 py-2.5 rounded-xl text-sm"
          style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.2);color:#f87171;">
          {{ error }}
        </div>
        <div class="space-y-4">
          <div>
            <label class="block text-xs font-medium mb-1.5 uppercase tracking-wide" style="color:#64748b;">Email</label>
            <input v-model="form.email" type="email" placeholder="john@example.com"
              class="w-full px-3 py-2.5 rounded-xl text-sm outline-none transition-all"
              style="background:#0f0f1a;border:1px solid #2d2d4e;color:#e2e8f0;" @keyup.enter="login"
              @focus="$event.target.style.borderColor = '#6366f1'" @blur="$event.target.style.borderColor = '#2d2d4e'" />
          </div>
          <div>
            <label class="block text-xs font-medium mb-1.5 uppercase tracking-wide"
              style="color:#64748b;">Password</label>
            <input v-model="form.password" type="password" placeholder="••••••••"
              class="w-full px-3 py-2.5 rounded-xl text-sm outline-none transition-all"
              style="background:#0f0f1a;border:1px solid #2d2d4e;color:#e2e8f0;" @keyup.enter="login"
              @focus="$event.target.style.borderColor = '#6366f1'" @blur="$event.target.style.borderColor = '#2d2d4e'" />
          </div>
          <button @click="login" :disabled="loading"
            class="w-full py-3 rounded-xl text-sm font-semibold transition-all hover:opacity-90 disabled:opacity-50 mt-2"
            style="background:linear-gradient(135deg,#6366f1,#8b5cf6);color:white;">
            {{ loading ? 'Signing in...' : 'Sign In' }}
          </button>
        </div>
        <div class="mt-4 p-3 rounded-xl text-xs text-center" style="background:#0f0f1a;color:#4a5568;">
          Demo: <span style="color:#818cf8;">john@example.com</span> /
          <span style="color:#818cf8;">password123</span>
        </div>
      </div>
      <p class="text-center text-xs mt-6" style="color:#4a5568;">TaskFlow v1.0.0 — Laravel 13 + Vue 3</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onBeforeMount } from 'vue'
import axios from 'axios'

const form = ref({ email: '', password: '' })
const loading = ref(false)
const error = ref(null)

// If already logged in, go to dashboard
onBeforeMount(() => {
  if (localStorage.getItem('token')) {
    window.location.replace('/')
  }
})

async function login() {
  if (!form.value.email || !form.value.password) {
    error.value = 'Please enter your email and password.'
    return
  }
  loading.value = true
  error.value = null
  try {
    const res = await axios.post('/api/auth/login', form.value)
    localStorage.setItem('token', res.data.token)
    localStorage.setItem('user', JSON.stringify(res.data.user))
    window.location.replace('/')
  } catch (e) {
    error.value = e.response?.data?.message || 'Invalid credentials.'
  } finally {
    loading.value = false
  }
}
</script>