<script setup>
import AppShell from '@/Layouts/AppShell.vue'
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

defineOptions({ layout: AppShell })

const page = usePage()
const user = computed(() => page.props.auth?.user)

const stats = [
    { label: 'Arquivos', value: '0', hint: 'Central de Arquivos' },
]

const quickActions = [
    { label: 'Central de Arquivos', desc: 'Ver arquivos', href: '/documents', icon: 'M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0118 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25' },
]
</script>

<template>
    <Head title="Dashboard" />

    <div class="space-y-6">

        <!-- Greeting -->
        <div>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">INTRANET | ZKTeco</p>
            <h1 class="mt-1 text-2xl font-semibold tracking-tight">Bom dia, {{ user?.name }}</h1>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div
                v-for="stat in stats"
                :key="stat.label"
                class="rounded-xl border border-zinc-200 bg-white p-5 text-center dark:border-zinc-800 dark:bg-zinc-900"
            >
                <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ stat.label }}</p>
                <p class="mt-2 text-3xl font-semibold tabular-nums">{{ stat.value }}</p>
                <p class="mt-1 text-xs text-zinc-400 dark:text-zinc-500">{{ stat.hint }}</p>
            </div>
        </div>

        <!-- Ações rápidas -->
        <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
            <h2 class="mb-4 text-sm font-medium text-zinc-900 dark:text-white">Ações rápidas</h2>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <Link
                    v-for="action in quickActions"
                    :key="action.label"
                    :href="action.href"
                    class="group flex flex-col items-center justify-center gap-2 rounded-lg border border-zinc-200 p-3 text-center transition-colors hover:border-brand-500/30 hover:bg-brand-500/5 dark:border-zinc-800 dark:hover:border-brand-500/20 dark:hover:bg-brand-500/5"
                >
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-zinc-100 transition-colors group-hover:bg-brand-500/10 dark:bg-zinc-800 dark:group-hover:bg-brand-500/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5 text-zinc-500 transition-colors group-hover:text-brand-600 dark:text-zinc-400 dark:group-hover:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" v-html="action.icon" />
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ action.label }}</p>
                        <p class="truncate text-xs text-zinc-400 dark:text-zinc-500">{{ action.desc }}</p>
                    </div>
                </Link>
            </div>
        </div>

    </div>
</template>
