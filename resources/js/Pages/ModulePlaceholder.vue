<script setup>
import AppShell from '@/Layouts/AppShell.vue'

defineOptions({
    layout: AppShell,
})

defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        required: true,
    },
})
</script>

<template>
    <Head :title="title" />

    <section class="space-y-2">
        <p class="text-sm text-zinc-500 dark:text-zinc-400">Módulo</p>
        <h1 class="text-2xl font-semibold tracking-tight">{{ title }}</h1>
        <p class="max-w-2xl text-sm text-zinc-600 dark:text-zinc-300">{{ description }}</p>
    </section>
</template>
