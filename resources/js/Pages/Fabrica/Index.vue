<script setup>
import AppShell from '@/Layouts/AppShell.vue'

defineOptions({ layout: AppShell })
</script>

<template>
    <Head title="Fábrica" />

    <div class="space-y-6">
        <div>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Área Operacional</p>
            <h1 class="mt-1 text-2xl font-semibold tracking-tight">Fábrica</h1>
        </div>

        <div class="flex min-h-64 flex-col items-center justify-center rounded-lg border border-zinc-200 bg-white px-6 py-12 text-center shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-brand-500/10 text-brand-700 dark:text-brand-400">
                <i class="bi bi-hammer text-3xl leading-none" aria-hidden="true" />
            </div>
            <h2 class="mt-4 text-lg font-semibold text-zinc-900 dark:text-zinc-100">Em desenvolvimento</h2>
            <p class="mt-2 max-w-sm text-sm text-zinc-500 dark:text-zinc-400">
                O conteúdo para o setor de Fábrica ainda está sendo preparado.
            </p>
        </div>
    </div>
</template>
