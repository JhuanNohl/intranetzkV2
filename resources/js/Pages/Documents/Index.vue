<script setup>
import AppShell from '@/Layouts/AppShell.vue'
import { router, usePage } from '@inertiajs/vue3'
import { reactive } from 'vue'

defineOptions({ layout: AppShell })

const props = defineProps({
    documents: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    selectedDepartment: {
        type: Object,
        default: null,
    },
    departments: {
        type: Array,
        required: true,
    },
    categories: {
        type: Array,
        required: true,
    },
})

const form = reactive({
    search: props.filters.search ?? '',
    department_id: props.filters.department_id ?? '',
    category_id: props.filters.category_id ?? '',
})
const page = usePage()

function applyFilters() {
    const url = props.selectedDepartment
        ? page.url.split('?')[0]
        : '/documents'

    router.get(url, form, {
        preserveState: true,
        replace: true,
    })
}

function clearFilters() {
    form.search = ''
    form.department_id = ''
    form.category_id = ''
    applyFilters()
}

function fileBadgeClass(label) {
    const normalized = (label || '').toLowerCase()

    if (['pdf'].includes(normalized)) {
        return 'bg-red-500/10 text-red-700 dark:bg-red-500/15 dark:text-red-300'
    }

    if (['doc', 'docx'].includes(normalized)) {
        return 'bg-blue-500/10 text-blue-700 dark:bg-blue-500/15 dark:text-blue-300'
    }

    if (['xls', 'xlsx', 'csv'].includes(normalized)) {
        return 'bg-emerald-500/10 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300'
    }

    if (['xml'].includes(normalized)) {
        return 'bg-amber-500/10 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300'
    }

    if (['html'].includes(normalized)) {
        return 'bg-orange-500/10 text-orange-700 dark:bg-orange-500/15 dark:text-orange-300'
    }

    if (['png', 'jpg', 'jpeg'].includes(normalized)) {
        return 'bg-violet-500/10 text-violet-700 dark:bg-violet-500/15 dark:text-violet-300'
    }

    if (['link'].includes(normalized)) {
        return 'bg-sky-500/10 text-sky-700 dark:bg-sky-500/15 dark:text-sky-300'
    }

    return 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300'
}
</script>

<template>
    <Head title="Arquivos" />

    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ selectedDepartment ? selectedDepartment.name : 'Central de Arquivos' }}
                </p>
                <h1 class="mt-1 text-2xl font-semibold tracking-tight">Arquivos</h1>
            </div>

            <Link
                :href="selectedDepartment ? `/documents/create?department_id=${selectedDepartment.id}` : '/documents/create'"
                class="inline-flex items-center justify-center rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-brand-700"
            >
                + Novo arquivo
            </Link>
        </div>

        <section class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
            <div
                class="grid grid-cols-1 gap-3"
                :class="selectedDepartment ? 'md:grid-cols-2' : 'md:grid-cols-3'"
            >
                <div class="relative">
                    <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-sm text-zinc-400" aria-hidden="true" />
                    <input
                        v-model="form.search"
                        type="search"
                        :placeholder="selectedDepartment ? `Buscar em ${selectedDepartment.name}` : 'Buscar arquivos'"
                        class="w-full rounded-lg border border-zinc-200 bg-white py-2.5 pl-9 pr-3 text-sm outline-none ring-brand-500/20 transition focus:border-brand-500 focus:ring-4 dark:border-white/10 dark:bg-zinc-950"
                        @keydown.enter="applyFilters"
                    >
                </div>

                <select
                    v-if="!selectedDepartment"
                    v-model="form.department_id"
                    class="rounded-lg border border-zinc-200 bg-white px-3 py-2.5 text-sm outline-none ring-brand-500/20 transition focus:border-brand-500 focus:ring-4 dark:border-white/10 dark:bg-zinc-950"
                >
                    <option value="">Departamento</option>
                    <option v-for="department in departments" :key="department.id" :value="department.id">
                        {{ department.name }}
                    </option>
                </select>

                <select
                    v-model="form.category_id"
                    class="rounded-lg border border-zinc-200 bg-white px-3 py-2.5 text-sm outline-none ring-brand-500/20 transition focus:border-brand-500 focus:ring-4 dark:border-white/10 dark:bg-zinc-950"
                >
                    <option value="">Categoria</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.name }}
                    </option>
                </select>

            </div>

            <div class="mt-4 flex justify-end gap-2">
                <button type="button" class="rounded-lg px-3 py-2 text-sm text-zinc-500 transition hover:bg-zinc-100 dark:hover:bg-zinc-800" @click="clearFilters">
                    Limpar
                </button>
                <button type="button" class="rounded-lg bg-zinc-900 px-3 py-2 text-sm font-medium text-white transition hover:bg-zinc-700 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200" @click="applyFilters">
                    Filtrar
                </button>
            </div>
        </section>

        <section class="overflow-hidden rounded-lg border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
            <div v-if="documents.data.length === 0" class="p-8 text-center">
                <p class="text-sm font-medium text-zinc-700 dark:text-zinc-200">Nenhum arquivo encontrado</p>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Crie um arquivo ou ajuste os filtros.</p>
            </div>

            <div v-else class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-800">
                    <thead class="bg-zinc-50 text-left text-xs uppercase tracking-wide text-zinc-500 dark:bg-zinc-950 dark:text-zinc-400">
                        <tr>
                            <th class="px-4 py-3 font-medium">Arquivo</th>
                            <th class="px-4 py-3 text-center font-medium">Departamento</th>
                            <th class="px-4 py-3 text-center font-medium">Categoria</th>
                            <th class="px-4 py-3 text-center font-medium">Atualizado</th>
                            <th class="px-4 py-3 text-center font-medium">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        <tr
                            v-for="document in documents.data"
                            :key="document.id"
                            class="group transition-colors hover:bg-brand-500/[0.03] dark:hover:bg-brand-500/[0.06]"
                        >
                            <td class="max-w-sm px-4 py-3">
                                <Link :href="`/documents/${document.id}`" class="flex items-start gap-3 cursor-pointer" :title="`Abrir ${document.title}`">
                                    <div
                                        class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-lg transition-transform group-hover:scale-105"
                                        :class="fileBadgeClass(document.file_meta?.label)"
                                    >
                                        <i :class="document.file_meta?.icon ?? 'bi bi-file-earmark'" class="text-xl leading-none" aria-hidden="true" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate font-medium text-zinc-900 transition-colors group-hover:text-brand-700 group-hover:underline dark:text-zinc-100 dark:group-hover:text-brand-400">{{ document.title }}</p>
                                        <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ document.summary || 'Sem resumo' }}</p>
                                        <span
                                            class="mt-1 inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-medium"
                                            :class="fileBadgeClass(document.file_meta?.label)"
                                        >
                                            {{ document.file_meta?.label ?? 'ARQ' }}
                                        </span>
                                    </div>
                                </Link>
                            </td>
                            <td class="px-4 py-3 text-center text-zinc-600 dark:text-zinc-300">{{ document.department?.name ?? '-' }}</td>
                            <td class="px-4 py-3 text-center text-zinc-600 dark:text-zinc-300">{{ document.category?.name ?? '-' }}</td>
                            <td class="px-4 py-3 text-center text-zinc-500 dark:text-zinc-400">{{ document.updated_at }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center gap-2">
                                    <Link :href="`/documents/${document.id}`" class="rounded-lg px-2 py-1 text-xs font-medium text-zinc-600 transition hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800">
                                        Abrir
                                    </Link>
                                    <p>|</p>
                                    <Link :href="`/documents/${document.id}/edit`" class="rounded-lg px-2 py-1 text-xs font-medium text-brand-700 transition hover:bg-brand-500/10 dark:text-brand-400">
                                        Editar
                                    </Link>
                                    <p>|</p>
                                    <a
                                        :href="document.file_url || '#'"
                                        :download="document.file_url ? (document.original_filename || true) : undefined"
                                        class="inline-flex h-7 w-7 items-center justify-center rounded-lg text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100"
                                        :class="{ 'invisible pointer-events-none': !document.file_url }"
                                        :title="`Baixar ${document.original_filename || 'arquivo'}`"
                                        aria-label="Baixar arquivo"
                                    >
                                        <i class="bi bi-download text-sm leading-none" aria-hidden="true" />
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <div v-if="documents.links?.length > 3" class="flex flex-wrap justify-end gap-1">
            <Link
                v-for="link in documents.links"
                :key="link.label"
                :href="link.url || '#'"
                class="rounded-lg px-3 py-2 text-sm transition"
                :class="[
                    link.active ? 'bg-brand-600 text-white' : 'text-zinc-600 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800',
                    !link.url ? 'pointer-events-none opacity-40' : ''
                ]"
                v-html="link.label"
            />
        </div>
    </div>
</template>
