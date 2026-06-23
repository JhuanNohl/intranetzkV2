<script setup>
import AppShell from '@/Layouts/AppShell.vue'
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

defineOptions({
    layout: AppShell,
})

const page = usePage()
const user = computed(() => page.props.auth?.user)
</script>

<template>
    <Head title="Dashboard" />

    <section class="space-y-6">
        <div>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Central operacional</p>
            <h1 class="text-2xl font-semibold tracking-tight">Bom dia, {{ user?.name }}</h1>
        </div>
    </section>
</template>
