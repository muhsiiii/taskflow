<template>
  <div class="min-h-screen flex items-center justify-center p-4 bg-slate-950 text-slate-100">
    <div class="relative w-full max-w-md rounded-3xl border border-white/10 bg-slate-900/95 p-8 shadow-2xl shadow-slate-950/40">
      <div class="mb-8 text-center">
        <p class="text-sm uppercase tracking-[0.35em] text-indigo-300">Create account</p>
        <h1 class="mt-4 text-3xl font-semibold text-white">Start your team workspace</h1>
        <p class="mt-2 text-sm text-slate-400">Register now and get access to TaskFlow for free.</p>
      </div>
      <div class="space-y-4">
        <div>
          <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Name</label>
          <input v-model="form.name" type="text" placeholder="Jane Doe"
            class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-slate-100 outline-none transition focus:border-indigo-500" />
        </div>
        <div>
          <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Email</label>
          <input v-model="form.email" type="email" placeholder="jane@example.com"
            class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-slate-100 outline-none transition focus:border-indigo-500" />
        </div>
        <div>
          <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Password</label>
          <input v-model="form.password" type="password" placeholder="Create a password"
            class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-slate-100 outline-none transition focus:border-indigo-500" />
        </div>
        <div>
          <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Confirm Password</label>
          <input v-model="form.password_confirmation" type="password" placeholder="Confirm password"
            class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-slate-100 outline-none transition focus:border-indigo-500" />
        </div>
      </div>
      <div v-if="error" class="mt-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 p-4 text-sm text-rose-200">{{ error }}</div>
      <button @click="register" :disabled="loading"
        class="mt-6 w-full rounded-2xl bg-indigo-500 px-4 py-3 text-sm font-semibold text-white transition hover:bg-indigo-400 disabled:opacity-50">
        {{ loading ? 'Creating account...' : 'Create account' }}
      </button>
      <div class="mt-5 text-center text-sm text-slate-400">Already have an account? <a href="/login" class="text-indigo-300 hover:text-white">Sign in</a></div>
      <div class="mt-3 text-center text-sm text-slate-400"><a href="/" class="text-indigo-300 hover:text-white">Back to home</a></div>
    </div>
  </div>
</template>

<script setup>
import { ref, onBeforeMount } from 'vue'
import axios from 'axios'

const form = ref({ name: '', email: '', password: '', password_confirmation: '' })
const loading = ref(false)
const error = ref(null)

async function validateAuth() {
  const token = localStorage.getItem('token')
  if (!token) {
    return
  }
  try {
    await axios.get('/api/auth/me', { headers: { Authorization: `Bearer ${token}` } })
    window.location.replace('/dashboard')
  } catch (e) {
    localStorage.removeItem('token')
    localStorage.removeItem('user')
  }
}

onBeforeMount(() => {
  validateAuth()
})

async function register() {
  if (!form.value.name || !form.value.email || !form.value.password || !form.value.password_confirmation) {
    error.value = 'Please fill in all fields.'
    return
  }
  if (form.value.password !== form.value.password_confirmation) {
    error.value = 'Passwords do not match.'
    return
  }
  loading.value = true
  error.value = null
  try {
    const res = await axios.post('/api/auth/register', form.value)
    localStorage.setItem('token', res.data.token)
    localStorage.setItem('user', JSON.stringify(res.data.user))
    window.location.replace('/dashboard')
  } catch (e) {
    error.value = e.response?.data?.message || 'Registration failed.'
  } finally {
    loading.value = false
  }
}
</script>
