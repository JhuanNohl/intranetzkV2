<script setup>
import AppShell from '@/Layouts/AppShell.vue'

defineOptions({ layout: AppShell })

defineProps({
    department: {
        type: Object,
        required: true,
    },
    cards: {
        type: Array,
        required: true,
    },
})

const categoryIcons = {
    'video-e-playlist': 'bi bi-play-circle-fill',
    licenciamento: 'bi bi-key-fill',
    seguranca: 'bi bi-shield-lock-fill',
    'metodologia-e-processos': 'bi bi-diagram-3-fill',
    'referencia-e-comandos': 'bi bi-terminal-fill',
    'multimidia-e-cftv': 'bi bi-camera-video-fill',
    'testes-e-qa': 'bi bi-bug-fill',
}

function categoryIcon(category) {
    return categoryIcons[category] ?? 'bi bi-mortarboard-fill'
}
</script>

<template>
    <Head title="Treinamentos" />

    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Área Técnica</p>
                <h1 class="mt-1 text-2xl font-semibold tracking-tight">Treinamentos</h1>
            </div>

            <div class="flex gap-2">
                <Link
                    :href="`/documents?department_id=${department.id}`"
                    class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-zinc-200 px-4 py-2 text-sm font-medium text-zinc-600 transition hover:bg-zinc-100 dark:border-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-800"
                >
                    <i class="bi bi-gear text-sm leading-none" aria-hidden="true" />
                    Gerenciar arquivos
                </Link>
                <Link
                    :href="`/documents/create?department_id=${department.id}`"
                    class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-brand-700"
                >
                    + Novo arquivo
                </Link>
            </div>
        </div>

        <div v-if="cards.length === 0" class="flex min-h-64 flex-col items-center justify-center rounded-lg border border-zinc-200 bg-white px-6 py-12 text-center shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-brand-500/10 text-brand-700 dark:text-brand-400">
                <i class="bi bi-mortarboard text-3xl leading-none" aria-hidden="true" />
            </div>
            <h2 class="mt-4 text-lg font-semibold text-zinc-900 dark:text-zinc-100">Nenhum treinamento cadastrado</h2>
            <p class="mt-2 max-w-sm text-sm text-zinc-500 dark:text-zinc-400">
                Use o botão "+ Novo arquivo" para cadastrar o primeiro treinamento deste setor.
            </p>
        </div>

        <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <article
                v-for="card in cards"
                :key="card.title"
                class="flex flex-col gap-3 rounded-lg border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900"
            >
                <div class="flex h-11 w-11 items-center justify-center rounded-full border-2 border-brand-500 text-brand-600 dark:text-brand-400">
                    <i :class="categoryIcon(card.category)" class="text-lg leading-none" aria-hidden="true" />
                </div>

                <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">{{ card.title }}</h3>
                <p v-if="card.summary" class="text-sm text-zinc-500 dark:text-zinc-400">{{ card.summary }}</p>

                <div class="mt-auto flex flex-wrap gap-2 pt-2">
                    <template v-for="link in card.links" :key="link.label">
                        <a
                            v-if="link.available"
                            :href="link.url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-lg bg-brand-600 px-3 py-2 text-sm font-medium text-white transition hover:bg-brand-700"
                        >
                            <i class="bi bi-play-fill text-sm leading-none" aria-hidden="true" />
                            {{ link.label }}
                        </a>
                        <span
                            v-else
                            class="inline-flex flex-1 cursor-not-allowed items-center justify-center gap-1.5 rounded-lg bg-brand-500/40 px-3 py-2 text-sm font-medium text-white/80"
                        >
                            <i class="bi bi-play-fill text-sm leading-none" aria-hidden="true" />
                            Em breve
                        </span>
                    </template>
                </div>
            </article>
        </div>
    </div>
</template>
