<script setup>
import AppShell from '@/Layouts/AppShell.vue'

defineOptions({ layout: AppShell })

// Substitua os hrefs pelos links reais das ferramentas.
const tools = [
    {
        name: 'Blip Desk',
        description: 'Atendimento e suporte ao cliente',
        href: 'https://zkteco.desk.blip.ai/',
        icon: 'bi bi-chat-dots-fill',
    },
    {
        name: 'TeamViewer',
        description: 'Download para acesso remoto',
        href: 'https://www.teamviewer.com/pt-br/download/windows/',
        icon: 'bi bi-display',
    },
    {
        name: 'Trello - Suporte Tecnico',
        description: 'Organizacao de tarefas e demandas',
        href: 'https://trello.com/b/ndbqxpCe/suporte-tecnico',
        icon: 'bi bi-kanban',
    },
    {
        name: 'Portal de Licencas ZKTeco',
        description: 'Gerenciamento de licencas de software',
        href: 'https://lic.zkteco.com/loginController.do?login',
        icon: 'bi bi-key-fill',
    },
    {
        name: 'Portal de Licencas ZKTeco (SG)',
        description: 'Sistema de gestao de licencas',
        href: 'https://sglic.zkteco.com/admin/go/login',
        icon: 'bi bi-shield-lock-fill',
    },
]

function isConfigured(href) {
    return href && href !== '#'
}
</script>

<template>
    <Head title="Suporte" />

    <div class="space-y-6">
        <div>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Area Tecnica</p>
            <h1 class="mt-1 text-2xl font-semibold tracking-tight">Suporte</h1>
        </div>

        <section class="overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <header class="flex items-center gap-2 border-b border-zinc-200 px-4 py-3 dark:border-zinc-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-zinc-900 dark:text-zinc-100" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26" />
                </svg>
                <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Ferramentas do Suporte</h2>
            </header>

            <div class="divide-y divide-zinc-200 px-4 dark:divide-zinc-800">
                <div
                    v-for="tool in tools"
                    :key="tool.name"
                    class="grid min-h-15 grid-cols-[2.5rem_minmax(0,1fr)] items-center gap-3 py-3 sm:grid-cols-[2.5rem_minmax(0,1fr)_auto]"
                >
                    <div class="flex h-9 w-9 items-center justify-center text-brand-700 dark:text-brand-400">
                        <i :class="tool.icon" class="text-xl leading-none" aria-hidden="true" />
                    </div>

                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ tool.name }}</p>
                        <p class="mt-1 truncate text-sm text-zinc-500 dark:text-zinc-400">{{ tool.description }}</p>
                    </div>

                    <a
                        :href="tool.href"
                        :target="isConfigured(tool.href) ? '_blank' : null"
                        :rel="isConfigured(tool.href) ? 'noopener noreferrer' : null"
                        class="col-start-2 inline-flex w-fit items-center gap-1 rounded-md bg-brand-700 px-2.5 py-1 text-xs font-semibold text-white transition-colors hover:bg-brand-600 sm:col-start-auto"
                        :class="!isConfigured(tool.href) ? 'cursor-default opacity-90' : ''"
                        @click="!isConfigured(tool.href) && $event.preventDefault()"
                    >
                        Link Externo
                        <i class="bi bi-box-arrow-up-right text-[0.8rem] leading-none" aria-hidden="true" />
                    </a>
                </div>
            </div>
        </section>
    </div>
</template>
