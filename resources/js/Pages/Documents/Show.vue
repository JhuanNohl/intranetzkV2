<script setup>
import AppShell from '@/Layouts/AppShell.vue'
import { router } from '@inertiajs/vue3'

defineOptions({ layout: AppShell })

const props = defineProps({
    document: {
        type: Object,
        required: true,
    },
})

function destroyDocument() {
    if (!confirm('Remover este arquivo?')) return

    router.delete(`/documents/${props.document.id}`)
}

function fileBadgeClass(label) {
    const normalized = (label || '').toLowerCase()

    if (['pdf'].includes(normalized)) return 'bg-red-500/10 text-red-700 dark:bg-red-500/15 dark:text-red-300'
    if (['doc', 'docx'].includes(normalized)) return 'bg-blue-500/10 text-blue-700 dark:bg-blue-500/15 dark:text-blue-300'
    if (['xls', 'xlsx', 'csv'].includes(normalized)) return 'bg-emerald-500/10 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300'
    if (['xml'].includes(normalized)) return 'bg-amber-500/10 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300'
    if (['html'].includes(normalized)) return 'bg-orange-500/10 text-orange-700 dark:bg-orange-500/15 dark:text-orange-300'
    if (['png', 'jpg', 'jpeg'].includes(normalized)) return 'bg-violet-500/10 text-violet-700 dark:bg-violet-500/15 dark:text-violet-300'
    if (['link'].includes(normalized)) return 'bg-sky-500/10 text-sky-700 dark:bg-sky-500/15 dark:text-sky-300'

    return 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300'
}
</script>

<template>
    <Head :title="document.title" />

    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ document.department?.name ?? 'Arquivos' }}
                </p>
                <h1 class="mt-1 text-2xl font-semibold tracking-tight">{{ document.title }}</h1>
                <p v-if="document.summary" class="mt-2 max-w-3xl text-sm text-zinc-500 dark:text-zinc-400">{{ document.summary }}</p>
            </div>

            <div class="flex gap-2">
                <Link :href="`/documents/${document.id}/edit`" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-brand-700">
                    Editar
                </Link>
                <button type="button" class="rounded-lg px-4 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10" @click="destroyDocument">
                    Remover
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-[minmax(0,1fr)_20rem]">
            <section class="space-y-5 rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                <div v-if="document.source_type === 'upload' && document.file_url" class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-800">
                    <div class="flex items-start gap-4">
                        <div
                            class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl"
                            :class="fileBadgeClass(document.file_meta?.label)"
                            :title="document.file_meta?.type"
                        >
                            <i :class="document.file_meta?.icon ?? 'bi bi-file-earmark'" class="text-3xl leading-none" aria-hidden="true" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Arquivo</p>
                                <span class="rounded-full px-2 py-0.5 text-[11px] font-medium" :class="fileBadgeClass(document.file_meta?.label)">
                                    {{ document.file_meta?.label ?? 'ARQ' }}
                                </span>
                            </div>
                            <p class="mt-1 truncate text-sm text-zinc-500 dark:text-zinc-400">{{ document.original_filename }}</p>
                            <a :href="document.file_url" target="_blank" class="mt-3 inline-flex items-center gap-2 rounded-lg bg-zinc-900 px-3 py-2 text-sm font-medium text-white transition hover:bg-zinc-700 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200">
                                <i class="bi bi-box-arrow-up-right text-sm leading-none" aria-hidden="true" />
                                Abrir arquivo
                            </a>
                        </div>
                    </div>
                </div>

                <div v-else-if="document.source_type === 'external' && document.external_url" class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-800">
                    <div class="flex items-start gap-4">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl" :class="fileBadgeClass(document.file_meta?.label)">
                            <i :class="document.file_meta?.icon ?? 'bi bi-link-45deg'" class="text-3xl leading-none" aria-hidden="true" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Link externo</p>
                            <a :href="document.external_url" target="_blank" class="mt-2 block break-all text-sm font-medium text-brand-700 dark:text-brand-400">
                                {{ document.external_url }}
                            </a>
                        </div>
                    </div>
                </div>

                <div v-if="document.content" class="prose prose-sm max-w-none whitespace-pre-line text-zinc-700 dark:text-zinc-300">
                    {{ document.content }}
                </div>

                <div v-if="!document.content && document.source_type !== 'external' && !document.file_url" class="rounded-lg border border-dashed border-zinc-300 p-8 text-center dark:border-zinc-800">
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Arquivo sem conteúdo anexado.</p>
                </div>
            </section>

            <aside class="space-y-5">
                <section class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                    <dl class="space-y-3 text-sm">
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-zinc-400">Status</dt>
                            <dd class="mt-1 font-medium text-zinc-900 dark:text-zinc-100">{{ document.status }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-zinc-400">Categoria</dt>
                            <dd class="mt-1 text-zinc-700 dark:text-zinc-300">{{ document.category?.name ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-zinc-400">Versão</dt>
                            <dd class="mt-1 text-zinc-700 dark:text-zinc-300">{{ document.version }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-zinc-400">Publicação</dt>
                            <dd class="mt-1 text-zinc-700 dark:text-zinc-300">{{ document.published_at ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-zinc-400">Expiração</dt>
                            <dd class="mt-1 text-zinc-700 dark:text-zinc-300">{{ document.expires_at ?? '-' }}</dd>
                        </div>
                    </dl>
                </section>

                <section class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                    <h2 class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Departamentos visíveis</h2>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <span
                            v-for="department in document.visible_departments"
                            :key="department.id"
                            class="rounded-full bg-zinc-100 px-2 py-1 text-xs text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300"
                        >
                            {{ department.name }}
                        </span>
                        <span v-if="document.visible_departments.length === 0" class="text-sm text-zinc-500 dark:text-zinc-400">Nenhum departamento adicional.</span>
                    </div>
                </section>

                <section class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                    <h2 class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Versões</h2>
                    <div class="mt-3 space-y-3">
                        <div v-for="version in document.versions" :key="version.id" class="rounded-lg border border-zinc-200 p-3 dark:border-zinc-800">
                            <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">v{{ version.version }}</p>
                            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ version.change_summary }}</p>
                            <p class="mt-2 text-xs text-zinc-400">{{ version.created_at }}</p>
                        </div>
                    </div>
                </section>
            </aside>
        </div>
    </div>
</template>
