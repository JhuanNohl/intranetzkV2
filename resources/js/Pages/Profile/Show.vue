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
    <Head title="Meu perfil" />

    <section class="space-y-6">
        <div>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Conta</p>
            <h1 class="text-2xl font-semibold tracking-tight">Meu perfil</h1>
        </div>

        <div class="max-w-2xl overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-white/10 dark:bg-zinc-900">
            <dl class="divide-y divide-zinc-200 dark:divide-white/10">
                <div class="grid gap-1 px-5 py-4 sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Nome</dt>
                    <dd class="text-sm sm:col-span-2">{{ user?.name }}</dd>
                </div>

                <div class="grid gap-1 px-5 py-4 sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">E-mail</dt>
                    <dd class="text-sm sm:col-span-2">{{ user?.email }}</dd>
                </div>

                <div class="grid gap-1 px-5 py-4 sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Perfil</dt>
                    <dd class="text-sm font-medium capitalize sm:col-span-2">{{ user?.profile }}</dd>
                </div>
            </dl>
        </div>
    </section>
</template>
