<script setup>
import { Head, useForm } from '@inertiajs/vue3'

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

function submit() {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>
    <Head title="Entrar" />

    <main class="grid min-h-screen place-items-center bg-zinc-50 px-6 py-10 text-zinc-950 dark:bg-zinc-950 dark:text-zinc-50">
        <section class="w-full max-w-sm">
            <div class="mb-8">
                <div class="mb-5 flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                        </svg>
                    </div>
                    <span class="text-sm font-semibold">IntranetZK</span>
                </div>
                <h1 class="text-2xl font-semibold tracking-tight">Acesse sua conta</h1>
                <p class="mt-1 text-sm text-zinc-500">Digite suas credenciais para continuar.</p>
            </div>

            <form class="space-y-5" @submit.prevent="submit">
                <div>
                    <label class="block text-sm font-medium" for="email">E-mail</label>
                    <input
                        id="email"
                        v-model="form.email"
                        autocomplete="username"
                        autofocus
                        class="mt-2 w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm outline-none ring-brand-500/20 transition focus:border-brand-500 focus:ring-4 dark:border-white/10 dark:bg-zinc-900"
                        type="email"
                    >
                    <p v-if="form.errors.email" class="mt-2 text-sm text-red-600 dark:text-red-400">
                        {{ form.errors.email }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium" for="password">Senha</label>
                    <input
                        id="password"
                        v-model="form.password"
                        autocomplete="current-password"
                        class="mt-2 w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm outline-none ring-brand-500/20 transition focus:border-brand-500 focus:ring-4 dark:border-white/10 dark:bg-zinc-900"
                        type="password"
                    >
                    <p v-if="form.errors.password" class="mt-2 text-sm text-red-600 dark:text-red-400">
                        {{ form.errors.password }}
                    </p>
                </div>

                <label class="flex items-center gap-3 text-sm text-zinc-600 dark:text-zinc-300">
                    <input
                        v-model="form.remember"
                        class="size-4 rounded border-zinc-300 text-brand-600 focus:ring-brand-500 dark:border-white/20 dark:bg-zinc-900"
                        type="checkbox"
                    >
                    Manter conectado
                </label>

                <button
                    class="w-full rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-brand-700 disabled:cursor-not-allowed disabled:opacity-60"
                    :disabled="form.processing"
                    type="submit"
                >
                    Entrar
                </button>
            </form>
        </section>
    </main>
</template>
