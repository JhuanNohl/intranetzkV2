<script setup>
import AppShell from '@/Layouts/AppShell.vue'
import { computed, reactive } from 'vue'

defineOptions({ layout: AppShell })

const props = defineProps({
    stats: {
        type: Object,
        required: true,
    },
    systems: {
        type: Array,
        required: true,
    },
    equipment: {
        type: Array,
        required: true,
    },
})

const filters = reactive({
    search: '',
})

const filteredEquipment = computed(() => {
    const search = filters.search.trim().toLowerCase()

    if (!search) return props.equipment

    return props.equipment.filter((item) => item.model.toLowerCase().includes(search))
})

function compatibleModel(item, systemId) {
    return item.compatibility?.[systemId] ?? null
}
</script>

<template>
    <Head title="Matriz de Integrações" />

    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    <Link href="/desenvolvimento" class="hover:underline">Desenvolvimento</Link> /
                </p>
                <h1 class="mt-1 text-2xl font-semibold tracking-tight">Matriz de Integrações</h1>
                <p class="mt-2 max-w-2xl text-sm text-zinc-500 dark:text-zinc-400">Compatibilidade entre sistemas e equipamentos.</p>
            </div>

            <div class="flex gap-2">
                <Link href="/desenvolvimento" class="inline-flex items-center justify-center gap-1.5 rounded-lg px-4 py-2 text-sm font-medium text-zinc-600 transition hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800">
                    <i class="bi bi-arrow-left text-sm leading-none" aria-hidden="true" />
                    Voltar
                </Link>
                <Link href="/desenvolvimento/equipamentos" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-zinc-200 px-4 py-2 text-sm font-medium text-zinc-600 transition hover:bg-zinc-100 dark:border-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-800">
                    <i class="bi bi-hdd-stack-fill text-sm leading-none" aria-hidden="true" />
                    Ver Equipamentos
                </Link>
            </div>
        </div>

        <section class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-lg border border-zinc-200 bg-white p-5 text-center shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <p class="text-3xl font-bold text-brand-700 dark:text-brand-400">{{ stats.systems_count }}</p>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Sistemas Integrados</p>
            </div>
            <div class="rounded-lg border border-zinc-200 bg-white p-5 text-center shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <p class="text-3xl font-bold text-brand-700 dark:text-brand-400">{{ stats.equipment_count }}</p>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Equipamentos Cadastrados</p>
            </div>
            <div class="rounded-lg border border-zinc-200 bg-white p-5 text-center shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <p class="text-3xl font-bold text-brand-700 dark:text-brand-400">{{ stats.compatibilities_count }}</p>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Compatibilidades Registradas</p>
            </div>
        </section>

        <section v-if="systems.length === 0" class="rounded-lg border border-dashed border-zinc-300 p-8 text-center dark:border-zinc-800">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Nenhuma matriz importada ainda.</p>
        </section>

        <section v-else class="overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="border-b border-zinc-200 p-4 dark:border-zinc-800">
                <div class="relative max-w-sm">
                    <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-sm text-zinc-400" aria-hidden="true" />
                    <input
                        v-model="filters.search"
                        type="search"
                        placeholder="Buscar equipamento"
                        class="w-full rounded-lg border border-zinc-200 bg-white py-2.5 pl-9 pr-3 text-sm outline-none ring-brand-500/20 transition focus:border-brand-500 focus:ring-4 dark:border-white/10 dark:bg-zinc-950"
                    >
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-xs dark:divide-zinc-800">
                    <thead class="bg-zinc-50 text-left uppercase tracking-wide text-zinc-500 dark:bg-zinc-950 dark:text-zinc-400">
                        <tr>
                            <th class="sticky left-0 z-[1] bg-zinc-50 px-3 py-3 font-medium dark:bg-zinc-950">Equipamento</th>
                            <th
                                v-for="system in systems"
                                :key="system.id"
                                class="min-w-32 px-3 py-3 font-medium"
                            >
                                {{ system.name }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        <tr v-for="item in filteredEquipment" :key="item.id">
                            <td class="sticky left-0 z-[1] whitespace-nowrap bg-white px-3 py-2 font-medium text-zinc-900 dark:bg-zinc-900 dark:text-zinc-100">
                                {{ item.model }}
                            </td>
                            <td
                                v-for="system in systems"
                                :key="system.id"
                                class="px-3 py-2 text-zinc-600 dark:text-zinc-300"
                                :class="compatibleModel(item, system.id) ? 'bg-emerald-500/5' : ''"
                            >
                                {{ compatibleModel(item, system.id) ?? '—' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</template>
