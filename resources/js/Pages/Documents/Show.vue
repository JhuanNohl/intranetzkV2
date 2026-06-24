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
    if (!confirm('Remover este documento?')) return

    router.delete(`/documents/${props.document.id}`)
}
</script>

<template>
    <Head :title="document.title" />

    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ document.department?.name ?? 'Documentos' }}
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
                <div v-if="document.source_type === 'upload' && document.file_url">
                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Arquivo</p>
                    <a :href="document.file_url" target="_blank" class="mt-2 inline-flex rounded-lg bg-zinc-900 px-3 py-2 text-sm font-medium text-white transition hover:bg-zinc-700 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200">
                        Abrir arquivo
                    </a>
                    <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">{{ document.original_filename }}</p>
                </div>

                <div v-else-if="document.source_type === 'external' && document.external_url">
                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Link externo</p>
                    <a :href="document.external_url" target="_blank" class="mt-2 break-all text-sm font-medium text-brand-700 dark:text-brand-400">
                        {{ document.external_url }}
                    </a>
                </div>

                <div v-if="document.content" class="prose prose-sm max-w-none whitespace-pre-line text-zinc-700 dark:text-zinc-300">
                    {{ document.content }}
                </div>

                <div v-if="!document.content && document.source_type !== 'external' && !document.file_url" class="rounded-lg border border-dashed border-zinc-300 p-8 text-center dark:border-zinc-800">
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Documento sem conteudo anexado.</p>
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
                            <dt class="text-xs uppercase tracking-wide text-zinc-400">Versao</dt>
                            <dd class="mt-1 text-zinc-700 dark:text-zinc-300">{{ document.version }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-zinc-400">Publicacao</dt>
                            <dd class="mt-1 text-zinc-700 dark:text-zinc-300">{{ document.published_at ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-zinc-400">Expiracao</dt>
                            <dd class="mt-1 text-zinc-700 dark:text-zinc-300">{{ document.expires_at ?? '-' }}</dd>
                        </div>
                    </dl>
                </section>

                <section class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                    <h2 class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Departamentos visiveis</h2>
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
                    <h2 class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Versoes</h2>
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
