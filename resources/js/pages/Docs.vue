<template>
  <AppLayout>
    <div class="space-y-8">
      <div class="flex flex-col gap-4 mb-8">
        <div class="flex items-center justify-between gap-4">
          <div>
            <p class="text-sm uppercase tracking-[0.35em] text-indigo-400">Documentation</p>
            <h1 class="text-3xl font-semibold" style="color:#f1f5f9;">What you get in TaskFlow</h1>
          </div>
          <a href="/projects" class="inline-flex items-center gap-2 rounded-full bg-indigo-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-400">
            🚀 Explore Projects
          </a>
        </div>
        <p class="text-slate-400 max-w-3xl">TaskFlow delivers a modern, launch-ready workspace for teams and solo users. Here is a practical feature set that makes your daily work more productive, organized, and visible.</p>
      </div>

      <div class="grid gap-4 lg:grid-cols-3">
        <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-6">
          <p class="text-sm uppercase tracking-[0.35em] text-slate-400">Workspace collaboration</p>
          <h2 class="mt-4 text-xl font-semibold text-white">Shared workspaces</h2>
          <p class="mt-3 text-slate-400">Create a shared workspace for your team, invite members, and split work into clear projects and tasks.</p>
        </div>
        <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-6">
          <p class="text-sm uppercase tracking-[0.35em] text-slate-400">Task planning</p>
          <h2 class="mt-4 text-xl font-semibold text-white">Kanban boards</h2>
          <p class="mt-3 text-slate-400">Manage tasks with drag-and-drop columns, priorities, due dates, and easy progress tracking.</p>
        </div>
        <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-6">
          <p class="text-sm uppercase tracking-[0.35em] text-slate-400">Project visibility</p>
          <h2 class="mt-4 text-xl font-semibold text-white">Progress dashboards</h2>
          <p class="mt-3 text-slate-400">See task completion, upcoming deadlines, and activity in one place so nothing slips through.</p>
        </div>
      </div>

      <section class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-6">
          <h3 class="text-xl font-semibold text-white">Core features</h3>
          <ul class="mt-5 space-y-4 text-slate-400 text-sm">
            <li>• Project creation and status tracking</li>
            <li>• Task assignments, priorities, and deadlines</li>
            <li>• Drag-and-drop kanban board management</li>
            <li>• Personal task center for assigned tasks</li>
            <li>• Organized activity feed and deadlines overview</li>
            <li>• Secure login with token-based auth</li>
          </ul>
        </div>
        <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-6">
          <h3 class="text-xl font-semibold text-white">Why it matters</h3>
          <p class="mt-5 text-slate-400 text-sm">TaskFlow is designed to give you enough structure for real work while staying simple and fast. It helps teams avoid chaos, keep priorities visible, and launch work immediately.</p>
          <div class="mt-6 grid gap-3 text-sm text-slate-400">
            <div class="rounded-2xl bg-slate-950/80 p-4 border border-white/5">Integrate with your daily workflow: board-based planning, fast quick actions, and clear task status.</div>
            <div class="rounded-2xl bg-slate-950/80 p-4 border border-white/5">Track work across multiple projects, maintain a single source of truth, and reduce update overhead.</div>
            <div class="rounded-2xl bg-slate-950/80 p-4 border border-white/5">Review what’s due next and keep team work aligned with a simple yet powerful dashboard.</div>
          </div>
        </div>
      </section>

      <section class="rounded-3xl border border-white/10 bg-slate-900/80 p-6">
        <h3 class="text-xl font-semibold text-white">What's included today</h3>
        <div class="mt-6 grid gap-4 md:grid-cols-2">
          <div>
            <p class="font-semibold text-white">Task Management</p>
            <ul class="mt-3 space-y-2 text-slate-400 text-sm">
              <li>• Task creation and editing</li>
              <li>• Priority and due date support</li>
              <li>• Status updates and in-review workflow</li>
              <li>• Personal task filtering</li>
            </ul>
          </div>
          <div>
            <p class="font-semibold text-white">Project Management</p>
            <ul class="mt-3 space-y-2 text-slate-400 text-sm">
              <li>• Create and organize projects</li>
              <li>• Track project progress</li>
              <li>• View project cards with due dates</li>
              <li>• Fast project creation modal</li>
            </ul>
          </div>
          <div>
            <p class="font-semibold text-white">Team Productivity</p>
            <ul class="mt-3 space-y-2 text-slate-400 text-sm">
              <li>• Shared workspace design</li>
              <li>• Activity feed and status overview</li>
              <li>• Dashboard insights</li>
              <li>• Easy navigation and quick actions</li>
            </ul>
          </div>
          <div>
            <p class="font-semibold text-white">Future-ready planning</p>
            <ul class="mt-3 space-y-2 text-slate-400 text-sm">
              <li>• Documentation built into the app</li>
              <li>• Ready for integrations and premium tiers</li>
              <li>• Scope for calendars, reports, and automations</li>
            </ul>
          </div>
        </div>
      </section>

      <section class="rounded-3xl border border-white/10 bg-slate-900/80 p-6">
        <h3 class="text-xl font-semibold text-white">Launch notes</h3>
        <p class="mt-4 text-slate-400 text-sm">This version focuses on polished usability and a strong core feature set. You can already use TaskFlow to build structure for projects, visualize task progress, and keep your day organized.</p>
      </section>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/components/AppLayout.vue'
</script>
