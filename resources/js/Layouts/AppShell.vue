<script setup>
import { computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

const navigation = [
    { label: 'Dashboard', href: '/' },
    { label: 'Chamados', href: '/chamados' },
    { label: 'Comunicados', href: '/comunicados' },
    { label: 'Base de conhecimento', href: '/conhecimento' },
    { label: 'Colaboradores', href: '/colaboradores' },
]

const page = usePage()
const user = computed(() => page.props.auth?.user)

function isActive(href) {
    return href === '/' ? page.url === '/' : page.url.startsWith(href)
}

function toggleTheme() {
    const current = document.documentElement.dataset.theme
    const next = current === 'dark' ? 'light' : 'dark'

    document.documentElement.dataset.theme = next
    document.documentElement.dataset.themePreference = next
    localStorage.setItem('intranetzk.theme', next)
}

function logout() {
    router.post('/logout')
}
</script>

<template>
    <div class="min-h-screen bg-zinc-50 text-zinc-950 dark:bg-zinc-950 dark:text-zinc-50">
        <aside
            class="fixed inset-y-0 left-0 hidden w-72 border-r border-zinc-200 bg-white/80 p-4 backdrop-blur xl:block dark:border-white/10 dark:bg-zinc-900/80">
            <div class="mb-8">
                <p class="text-xs font-medium uppercase tracking-[0.2em] text-zinc-500">IntranetZK</p>
                <h1 class="mt-2 text-lg font-semibold">Operação interna</h1>
            </div>

            <nav class="space-y-1">
                <Link
                    v-for="item in navigation"
                    :key="item.href"
                    :href="item.href"
                    class="block rounded-xl px-3 py-2 text-sm transition hover:bg-zinc-100 hover:text-zinc-950 dark:hover:bg-white/10 dark:hover:text-white"
                    :class="isActive(item.href)
                        ? 'bg-zinc-100 font-medium text-zinc-950 dark:bg-white/10 dark:text-white'
                        : 'text-zinc-600 dark:text-zinc-300'"
                >
                    {{ item.label }}
                </Link>
            </nav>
        </aside>

        <div class="xl:pl-72">
            <header
                class="sticky top-0 z-10 border-b border-zinc-200 bg-zinc-50/80 px-6 py-4 backdrop-blur dark:border-white/10 dark:bg-zinc-950/80">
                <div class="flex items-center justify-between gap-4">
                    <button
                        class="rounded-xl border border-zinc-200 bg-white px-4 py-2 text-sm text-zinc-500 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-300">
                        Buscar em tudo...
                        <span class="ml-3 text-xs">Ctrl K</span>
                    </button>

                    <div class="flex items-center gap-3">
                        <div class="hidden text-right sm:block">
                            <p class="text-sm font-medium">{{ user?.name }}</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ user?.email }}</p>
                        </div>

                        <button
                            type="button"
                            class="rounded-xl border border-zinc-200 bg-white px-4 py-2 text-sm dark:border-white/10 dark:bg-zinc-900"
                            @click="toggleTheme"
                        >
                            Alternar tema
                        </button>

                        <button
                            type="button"
                            class="rounded-xl border border-zinc-200 bg-white px-4 py-2 text-sm text-zinc-600 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-300"
                            @click="logout"
                        >
                            Sair
                        </button>
                    </div>
                </div>
            </header>

            <main class="p-6">
                <slot />
            </main>
        </div>
    </div>
</template>
