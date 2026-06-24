<script setup>
import AppShell from '@/Layouts/AppShell.vue'
import { router } from '@inertiajs/vue3'
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
    statusOptions: {
        type: Array,
        required: true,
    },
    sourceTypeOptions: {
        type: Array,
        required: true,
    },
})

const form = reactive({
    search: props.filters.search ?? '',
    department_id: props.filters.department_id ?? '',
    category_id: props.filters.category_id ?? '',
    status: props.filters.status ?? '',
    source_type: props.filters.source_type ?? '',
})

function applyFilters() {
    const url = props.selectedDepartment
        ? `/departments/${props.selectedDepartment.slug}/documents`
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
    form.status = ''
    form.source_type = ''
    applyFilters()
}

function badgeClass(status) {
    return {
        draft: 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300',
        published: 'bg-brand-500/10 text-brand-700 dark:bg-brand-500/15 dark:text-brand-400',
        archived: 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300',
    }[status] ?? 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300'
}
</script>

<template>
    <Head title="Documentos" />

    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ selectedDepartment ? selectedDepartment.name : 'Base de conhecimento' }}
                </p>
                <h1 class="mt-1 text-2xl font-semibold tracking-tight">Documentos</h1>
            </div>

            <Link
                :href="selectedDepartment ? `/documents/create?department_id=${selectedDepartment.id}` : '/documents/create'"
                class="inline-flex items-center justify-center rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-brand-700"
            >
                Novo documento
            </Link>
        </div>

        <section class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
            <div class="grid grid-cols-1 gap-3 md:grid-cols-5">
                <input
                    v-model="form.search"
                    type="search"
                    placeholder="Buscar"
                    class="rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm outline-none ring-brand-500/20 transition focus:border-brand-500 focus:ring-4 dark:border-white/10 dark:bg-zinc-950"
                    @keydown.enter="applyFilters"
                >

                <select
                    v-if="!selectedDepartment"
                    v-model="form.department_id"
                    class="rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm outline-none ring-brand-500/20 transition focus:border-brand-500 focus:ring-4 dark:border-white/10 dark:bg-zinc-950"
                >
                    <option value="">Departamento</option>
                    <option v-for="department in departments" :key="department.id" :value="department.id">
                        {{ department.name }}
                    </option>
                </select>

                <select
                    v-model="form.category_id"
                    class="rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm outline-none ring-brand-500/20 transition focus:border-brand-500 focus:ring-4 dark:border-white/10 dark:bg-zinc-950"
                >
                    <option value="">Categoria</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.name }}
                    </option>
                </select>

                <select
                    v-model="form.status"
                    class="rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm outline-none ring-brand-500/20 transition focus:border-brand-500 focus:ring-4 dark:border-white/10 dark:bg-zinc-950"
                >
                    <option value="">Status</option>
                    <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                        {{ option.label }}
                    </option>
                </select>

                <select
                    v-model="form.source_type"
                    class="rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm outline-none ring-brand-500/20 transition focus:border-brand-500 focus:ring-4 dark:border-white/10 dark:bg-zinc-950"
                >
                    <option value="">Origem</option>
                    <option v-for="option in sourceTypeOptions" :key="option.value" :value="option.value">
                        {{ option.label }}
                    </option>
                </select>
            </div>

            <div class="mt-3 flex justify-end gap-2">
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
                <p class="text-sm font-medium text-zinc-700 dark:text-zinc-200">Nenhum documento encontrado</p>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Crie um documento ou ajuste os filtros.</p>
            </div>

            <div v-else class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-800">
                    <thead class="bg-zinc-50 text-left text-xs uppercase tracking-wide text-zinc-500 dark:bg-zinc-950 dark:text-zinc-400">
                        <tr>
                            <th class="px-4 py-3 font-medium">Documento</th>
                            <th class="px-4 py-3 font-medium">Departamento</th>
                            <th class="px-4 py-3 font-medium">Categoria</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Atualizado</th>
                            <th class="px-4 py-3 text-right font-medium">Acoes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        <tr v-for="document in documents.data" :key="document.id">
                            <td class="max-w-sm px-4 py-3">
                                <p class="truncate font-medium text-zinc-900 dark:text-zinc-100">{{ document.title }}</p>
                                <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ document.summary || 'Sem resumo' }}</p>
                            </td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ document.department?.name ?? '-' }}</td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ document.category?.name ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2 py-1 text-xs font-medium" :class="badgeClass(document.status)">
                                    {{ document.status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">{{ document.updated_at }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <Link :href="`/documents/${document.id}`" class="rounded-lg px-2 py-1 text-xs font-medium text-zinc-600 transition hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800">
                                        Abrir
                                    </Link>
                                    <Link :href="`/documents/${document.id}/edit`" class="rounded-lg px-2 py-1 text-xs font-medium text-brand-700 transition hover:bg-brand-500/10 dark:text-brand-400">
                                        Editar
                                    </Link>
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
