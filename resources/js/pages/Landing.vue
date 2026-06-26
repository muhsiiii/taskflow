<template>
  <div class="min-h-screen bg-slate-950 text-slate-100 font-sans overflow-hidden">
    <header class="relative overflow-hidden">
      <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(99,102,241,0.25),_transparent_35%)]"></div>
      <div class="relative z-10 max-w-7xl mx-auto px-6 py-10 lg:px-8">
        <nav class="flex items-center justify-between gap-6">
          <div class="flex items-center gap-3">
            <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-500 text-xl">⚡</span>
            <div>
              <p class="text-base font-semibold tracking-tight text-white">TaskFlow</p>
              <p class="text-xs text-slate-400">Modern work management for teams and individuals</p>
            </div>
          </div>
          <div class="hidden md:flex items-center gap-6 text-sm text-slate-300">
            <a href="#features" class="hover:text-white">Features</a>
            <a href="#pricing" class="hover:text-white">Pricing</a>
            <a href="#about" class="hover:text-white">About</a>
            <a href="/docs" class="hover:text-white">Docs</a>
            <a href="/login" class="hover:text-white">Sign In</a>
          </div>
          <a href="/register" class="inline-flex items-center justify-center rounded-full bg-white px-5 py-2.5 text-sm font-semibold text-slate-950 shadow-lg shadow-slate-900/30 transition hover:-translate-y-0.5">Get Started</a>
        </nav>

        <main class="mt-20 grid gap-16 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
          <div class="max-w-2xl">
            <div class="inline-flex items-center gap-2 rounded-full bg-slate-900/80 px-4 py-2 text-sm text-slate-300 ring-1 ring-white/10">
              <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-indigo-500/20 text-indigo-200">⭐</span>
              Launch-ready productivity for public teams, now free.
            </div>
            <h1 class="mt-8 text-5xl font-semibold tracking-tight text-white sm:text-6xl">Powerful task management that grows with your team.</h1>
            <p class="mt-6 text-lg leading-8 text-slate-300">TaskFlow combines Kanban planning, shared workspaces, intelligent workflows, and polished team collaboration into one deploy-ready app.</p>
            <div class="mt-10 flex flex-col gap-4 sm:flex-row sm:items-center">
              <a href="/register" class="inline-flex items-center justify-center rounded-full bg-indigo-500 px-6 py-3 text-base font-semibold text-white shadow-lg shadow-indigo-500/20 transition hover:bg-indigo-400">Create free account</a>
              <a href="/login" class="inline-flex items-center justify-center rounded-full border border-slate-700 bg-slate-900 px-6 py-3 text-base font-semibold text-slate-100 transition hover:border-slate-500">Sign in</a>
            </div>
            <div class="mt-10 grid gap-4 sm:grid-cols-2">
              <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-5">
                <p class="text-sm uppercase tracking-[0.3em] text-slate-400">Works great for</p>
                <p class="mt-4 text-xl font-semibold text-white">Teams of any size</p>
                <p class="mt-2 text-slate-400">From solo planners to full product teams, TaskFlow scales effortlessly.</p>
              </div>
              <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-5">
                <p class="text-sm uppercase tracking-[0.3em] text-slate-400">Launch ready</p>
                <p class="mt-4 text-xl font-semibold text-white">Public deployment ready</p>
                <p class="mt-2 text-slate-400">Designed to ship now with clean UI, auth, API and workspace support.</p>
              </div>
            </div>
          </div>
          <div class="relative">
            <div class="absolute inset-0 rounded-[2rem] bg-slate-800/80 blur-3xl"></div>
            <div class="relative overflow-hidden rounded-[2rem] border border-white/10 bg-slate-950/90 shadow-2xl shadow-slate-950/40">
              <img src="https://images.unsplash.com/photo-1556157382-97eda2d62296?auto=format&fit=crop&w=900&q=80" alt="Product screenshot" class="h-full w-full object-cover" />
            </div>
          </div>
        </main>
      </div>
    </header>

    <section id="features" class="relative border-t border-white/5 bg-slate-950/95 py-20">
      <div class="relative z-10 mx-auto max-w-7xl px-6 lg:px-8">
        <div class="grid gap-10 lg:grid-cols-3">
          <div class="space-y-4">
            <p class="text-sm font-semibold uppercase tracking-[0.35em] text-indigo-500">Features</p>
            <h2 class="text-4xl font-semibold text-white">Everything teams need to ship work with confidence.</h2>
            <p class="text-slate-400">TaskFlow provides built-in workspace collaboration, project tracking, task boards, and fast authentication in one package.</p>
          </div>
          <div class="grid gap-6 sm:grid-cols-2 lg:col-span-2">
            <div class="rounded-3xl border border-white/5 bg-slate-900/80 p-6">
              <p class="text-sm uppercase tracking-[0.3em] text-slate-400">Kanban Board</p>
              <h3 class="mt-4 text-xl font-semibold text-white">Drag & drop workflows</h3>
              <p class="mt-3 text-slate-400">Organize work across todo, in progress, review and done boards for clear progress visibility.</p>
            </div>
            <div class="rounded-3xl border border-white/5 bg-slate-900/80 p-6">
              <p class="text-sm uppercase tracking-[0.3em] text-slate-400">Workspaces</p>
              <h3 class="mt-4 text-xl font-semibold text-white">Shared workspace control</h3>
              <p class="mt-3 text-slate-400">Create workspaces, invite members, and manage projects in a centralized collaboration space.</p>
            </div>
            <div class="rounded-3xl border border-white/5 bg-slate-900/80 p-6">
              <p class="text-sm uppercase tracking-[0.3em] text-slate-400">Task management</p>
              <h3 class="mt-4 text-xl font-semibold text-white">Rich task details</h3>
              <p class="mt-3 text-slate-400">Assign tasks, set priorities, add due dates, and track task status across your team.</p>
            </div>
            <div class="rounded-3xl border border-white/5 bg-slate-900/80 p-6">
              <p class="text-sm uppercase tracking-[0.3em] text-slate-400">Future-ready</p>
              <h3 class="mt-4 text-xl font-semibold text-white">Ready for launch</h3>
              <p class="mt-3 text-slate-400">Designed for public availability now, with future plans for premium features and pricing tiers.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="pricing" class="mx-auto max-w-7xl px-6 py-20 lg:px-8">
      <div class="text-center">
        <p class="text-sm font-semibold uppercase tracking-[0.35em] text-indigo-500">Pricing</p>
        <h2 class="mt-4 text-4xl font-semibold text-white">Public launch is free today.</h2>
        <p class="mt-4 text-slate-400 max-w-2xl mx-auto">Access all core work management features at no cost during launch. Premium pricing will arrive later with advanced team and automation plans.</p>
      </div>

      <div class="mt-14 grid gap-6 lg:grid-cols-3">
        <div class="rounded-3xl border border-white/5 bg-slate-900/80 p-8 text-center">
          <p class="text-sm uppercase tracking-[0.35em] text-slate-400">Starter</p>
          <p class="mt-6 text-5xl font-semibold text-white">Free</p>
          <p class="mt-4 text-slate-400">Perfect for individuals and small teams just getting started.</p>
          <ul class="mt-8 space-y-3 text-left text-slate-400">
            <li>Unlimited tasks</li>
            <li>Workspace collaboration</li>
            <li>Kanban boards</li>
            <li>Secure login and account</li>
          </ul>
          <a href="/register" class="mt-10 inline-flex w-full items-center justify-center rounded-full bg-indigo-500 px-6 py-3 text-sm font-semibold text-white transition hover:bg-indigo-400">Get started free</a>
        </div>
        <div class="rounded-3xl border border-white/5 bg-slate-900/80 p-8 text-center">
          <p class="text-sm uppercase tracking-[0.35em] text-slate-400">Premium</p>
          <p class="mt-6 text-5xl font-semibold text-white">Coming soon</p>
          <p class="mt-4 text-slate-400">Advanced workspace controls, member roles, and premium support.</p>
          <ul class="mt-8 space-y-3 text-left text-slate-400">
            <li>Priority support</li>
            <li>Custom workflows</li>
            <li>Advanced reporting</li>
          </ul>
          <button disabled class="mt-10 inline-flex w-full items-center justify-center rounded-full border border-slate-700 px-6 py-3 text-sm font-semibold text-slate-400">Available later</button>
        </div>
        <div class="rounded-3xl border border-white/5 bg-slate-900/80 p-8 text-center">
          <p class="text-sm uppercase tracking-[0.35em] text-slate-400">Enterprise</p>
          <p class="mt-6 text-5xl font-semibold text-white">Coming soon</p>
          <p class="mt-4 text-slate-400">Scale with advanced security and team governance features.</p>
          <ul class="mt-8 space-y-3 text-left text-slate-400">
            <li>Team roles</li>
            <li>Audit logs</li>
            <li>Enterprise onboarding</li>
          </ul>
          <button disabled class="mt-10 inline-flex w-full items-center justify-center rounded-full border border-slate-700 px-6 py-3 text-sm font-semibold text-slate-400">Available later</button>
        </div>
      </div>
    </section>

    <section id="about" class="border-t border-white/5 bg-slate-950/95 py-20">
      <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="grid gap-10 lg:grid-cols-3">
          <div>
            <p class="text-sm uppercase tracking-[0.35em] text-indigo-500">Why TaskFlow</p>
            <h2 class="mt-4 text-4xl font-semibold text-white">A polished productivity experience built for launch.</h2>
          </div>
          <div class="space-y-6 text-slate-400 lg:col-span-2">
            <p>TaskFlow isn’t just a demo: it has authenticated user flows, workspace isolation, project and task management, and a clean public interface for visitors.</p>
            <p>From the dashboard to task details, everything is designed to feel like a modern SaaS product.</p>
            <p>Start free now, then upgrade later when premium workspaces, member roles, and automation are added.</p>
          </div>
        </div>
      </div>
    </section>

    <footer class="mx-auto max-w-7xl px-6 py-12 text-slate-400 lg:px-8">
      <div class="flex flex-col gap-6 border-t border-white/5 pt-8 text-sm sm:flex-row sm:items-center sm:justify-between">
        <p>© 2026 TaskFlow. Built for public launch.</p>
        <div class="flex flex-wrap gap-4">
          <a href="/" class="hover:text-white">Home</a>
          <a href="/login" class="hover:text-white">Login</a>
          <a href="/register" class="hover:text-white">Register</a>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { onBeforeMount } from 'vue'
import axios from 'axios'

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
</script>
