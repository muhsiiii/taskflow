<template>
  <AppLayout>
    <div class="space-y-8">
      <div class="flex flex-col gap-4 lg:flex-row lg:justify-between lg:items-end">
        <div>
          <p class="text-sm uppercase tracking-[0.35em] text-indigo-400">Dashboard guide</p>
          <h1 class="text-3xl font-semibold" style="color:#f1f5f9;">Working in TaskFlow</h1>
          <p class="text-slate-400 max-w-3xl">This guide is for active users who are already inside the app. Learn what each section does and how to make the most of your workspace.</p>
        </div>
        <a href="/projects" class="rounded-full bg-indigo-500 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-400">Jump to Projects</a>
      </div>

      <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-6">
          <h2 class="text-xl font-semibold text-white">Dashboard</h2>
          <p class="mt-4 text-slate-400 text-sm">Your dashboard surfaces the most important work: task progress, project health, upcoming deadlines, and quick actions.</p>
          <ul class="mt-5 space-y-3 text-slate-400 text-sm">
            <li>• Track overall task completion for the workspace.</li>
            <li>• See deadlines for the next 7 days.</li>
            <li>• Find overdue work quickly.</li>
            <li>• Use quick actions to create tasks, start projects, or open help.</li>
          </ul>
        </div>

        <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-6">
          <h2 class="text-xl font-semibold text-white">Projects</h2>
          <p class="mt-4 text-slate-400 text-sm">Projects are containers for related tasks. They let you organize a campaign, feature, or initiative with its own status, due date, and task board.</p>
          <ul class="mt-5 space-y-3 text-slate-400 text-sm">
            <li>• Create and edit project details.</li>
            <li>• Add project members and assign tasks.</li>
            <li>• Track work using a dedicated project board.</li>
            <li>• Use project status and due dates to keep goals visible.</li>
          </ul>
        </div>
      </div>

      <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-6">
        <h2 class="text-xl font-semibold text-white">Task center</h2>
        <p class="mt-4 text-slate-400 text-sm">Your personal task page shows tasks assigned to you across all projects, so you can focus on what you own.</p>
        <ul class="mt-5 space-y-3 text-slate-400 text-sm">
          <li>• Filter tasks by status.</li>
          <li>• Search tasks by title or description.</li>
          <li>• Mark tasks done quickly.</li>
          <li>• Stay in sync with due dates and priorities.</li>
        </ul>
      </div>

      <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-6">
        <h2 class="text-xl font-semibold text-white">Your next steps</h2>
        <ul class="mt-5 space-y-3 text-slate-400 text-sm">
          <li>• Create a project and define a clear goal.</li>
          <li>• Add tasks to the project and assign owners.</li>
          <li>• Use the dashboard to watch deadlines and progress.</li>
          <li>• Return here anytime to refresh how to use the app.</li>
        </ul>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/components/AppLayout.vue'
</script>
