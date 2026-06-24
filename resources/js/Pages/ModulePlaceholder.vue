<script setup>
import AppShell from '@/Layouts/AppShell.vue'

defineOptions({ layout: AppShell })

defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        required: true,
    },
    group: {
        type: String,
        default: null,
    },
})
</script>

<template>
    <Head :title="title" />

    <div class="space-y-6">

        <!-- Header -->
        <div>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                {{ group ?? 'Módulo' }}
            </p>
            <h1 class="mt-1 text-2xl font-semibold tracking-tight">{{ title }}</h1>
            <p class="mt-1 max-w-2xl text-sm text-zinc-500 dark:text-zinc-400">{{ description }}</p>
        </div>

        <!-- Em construção -->
        <div class="flex min-h-64 flex-col items-center justify-center rounded-xl border border-dashed border-zinc-300 bg-zinc-50 text-center dark:border-zinc-800 dark:bg-zinc-900/50">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-500/10 dark:bg-brand-500/10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-brand-600 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z" />
                </svg>
            </div>
            <p class="mt-4 text-sm font-medium text-zinc-700 dark:text-zinc-300">Em desenvolvimento</p>
            <p class="mt-1 text-xs text-zinc-400 dark:text-zinc-500">Este módulo estará disponível em breve.</p>
        </div>

    </div>
</template>
