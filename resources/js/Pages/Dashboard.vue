<script setup>
import AppShell from '@/Layouts/AppShell.vue'
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

defineOptions({ layout: AppShell })

const page = usePage()
const user = computed(() => page.props.auth?.user)

const stats = [
    { label: 'Chamados abertos',    value: '0', hint: 'Nenhum pendente'       },
    { label: 'Comunicados',         value: '0', hint: 'Tudo lido'             },
    { label: 'Aprovações',          value: '0', hint: 'Em dia'                },
    { label: 'Documentos',          value: '0', hint: 'Base de conhecimento'  },
]

const quickActions = [
    { label: 'Novo chamado',       desc: 'Abrir solicitação', href: '/chamados',     icon: 'M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a3 3 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z' },
    { label: 'Comunicados',        desc: 'Ver comunicados',   href: '/comunicados',  icon: 'M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 01-1.44-4.282m3.102.069a18.03 18.03 0 01-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 018.835 2.535M10.34 6.66a23.847 23.847 0 008.835-2.535m0 0A23.74 23.74 0 0018.795 3m.38 1.125a23.91 23.91 0 011.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 001.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 010 3.46' },
    { label: 'Colaboradores',      desc: 'Ver equipe',        href: '/colaboradores',icon: 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z' },
    { label: 'Base de conhecimento',desc: 'Ver documentos',   href: '/documents', icon: 'M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0118 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25' },
]
</script>

<template>
    <Head title="Dashboard" />

    <div class="space-y-6">

        <!-- Greeting -->
        <div>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Central operacional</p>
            <h1 class="mt-1 text-2xl font-semibold tracking-tight">Bom dia, {{ user?.name }}</h1>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div
                v-for="stat in stats"
                :key="stat.label"
                class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900"
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
                    class="group flex items-center gap-3 rounded-lg border border-zinc-200 p-3 transition-colors hover:border-brand-500/30 hover:bg-brand-500/5 dark:border-zinc-800 dark:hover:border-brand-500/20 dark:hover:bg-brand-500/5"
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
