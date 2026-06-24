<script setup>
import { computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

const NAV_ICONS = {
    home:      'M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25',
    briefcase: 'M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z',
    people:    'M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z',
    currency:  'M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    code:      'M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5',
    headset:   'M6 18.75a.75.75 0 001.5 0v-6a.75.75 0 00-1.5 0v6zM18 18.75a.75.75 0 001.5 0v-6a.75.75 0 00-1.5 0v6zM4.5 9.75v1.5a7.5 7.5 0 0015 0v-1.5M4.5 9.75A2.25 2.25 0 016.75 7.5h10.5a2.25 2.25 0 012.25 2.25',
    monitor:   'M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25',
    academic:  'M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5',
    building:  'M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z',
    wrench:    'M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z',
    cube:      'M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9',
}

const groups = [
    {
        items: [
            { label: 'Início', href: '/', icon: 'home' },
        ],
    },
    {
        label: 'Corporativo',
        items: [
            { label: 'Comercial',            href: '/comercial',           icon: 'briefcase' },
            { label: 'Departamento Pessoal', href: '/departamento-pessoal',icon: 'people'    },
            { label: 'Financeiro',           href: '/financeiro',          icon: 'currency'  },
        ],
    },
    {
        label: 'Área Técnica',
        items: [
            { label: 'Desenvolvimento', href: '/desenvolvimento', icon: 'code'     },
            { label: 'Suporte',         href: '/suporte',         icon: 'headset'  },
            { label: 'T.I.',            href: '/ti',              icon: 'monitor'  },
            { label: 'Treinamentos',    href: '/treinamentos',    icon: 'academic' },
        ],
    },
    {
        label: 'Operacional',
        items: [
            { label: 'Fábrica',    href: '/fabrica',    icon: 'building' },
            { label: 'Manutenção', href: '/manutencao', icon: 'wrench'   },
            { label: 'Produtos',   href: '/produtos',   icon: 'cube'     },
        ],
    },
]

const page = usePage()
const user = computed(() => page.props.auth?.user)

function initials(name) {
    if (!name) return 'U'
    return name.split(' ').slice(0, 2).map(n => n[0]).join('').toUpperCase()
}

function isActive(href) {
    if (href === '/') return page.url === '/'
    return page.url === href || page.url.startsWith(href + '/')
}

function toggleTheme() {
    const next = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark'
    document.documentElement.dataset.theme = next
    localStorage.setItem('intranetzk.theme', next)
}

function logout() {
    router.post('/logout')
}
</script>

<template>
    <div class="min-h-screen bg-zinc-50 text-zinc-900 dark:bg-zinc-950 dark:text-zinc-50">

        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-20 hidden w-56 flex-col border-r border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-950 xl:flex">

            <!-- Logo -->
            <div class="flex items-center gap-3 border-b border-zinc-200 px-4 py-4 dark:border-zinc-800">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-brand-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                </div>
                <span class="text-sm font-semibold text-zinc-900 dark:text-white">IntranetZK</span>
            </div>

            <!-- Nav -->
            <nav class="flex-1 space-y-4 overflow-y-auto px-3 py-4">
                <div v-for="group in groups" :key="group.label ?? '_root'">
                    <p
                        v-if="group.label"
                        class="mb-1 px-3 text-[10px] font-semibold uppercase tracking-widest text-brand-600 dark:text-brand-500"
                    >
                        {{ group.label }}
                    </p>
                    <div class="space-y-0.5">
                        <Link
                            v-for="item in group.items"
                            :key="item.href"
                            :href="item.href"
                            class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors"
                            :class="isActive(item.href)
                                ? 'bg-brand-500/10 font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-400'
                                : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100'"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4.5 w-4.5 shrink-0"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.5"
                                v-html="NAV_ICONS[item.icon]"
                            />
                            {{ item.label }}
                        </Link>
                    </div>
                </div>
            </nav>

            <!-- User -->
            <div class="border-t border-zinc-200 px-3 py-3 dark:border-zinc-800">
                <Link
                    href="/meu-perfil"
                    class="flex items-center gap-3 rounded-lg px-2 py-2 transition-colors hover:bg-zinc-100 dark:hover:bg-zinc-800"
                    :class="isActive('/meu-perfil') ? 'bg-zinc-100 dark:bg-zinc-800' : ''"
                >
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-500/15 text-xs font-semibold text-brand-600 dark:text-brand-400">
                        {{ initials(user?.name) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium text-zinc-900 dark:text-white">{{ user?.name }}</p>
                        <p class="truncate text-xs capitalize text-zinc-400">{{ user?.profile }}</p>
                    </div>
                </Link>
            </div>
        </aside>

        <!-- Main -->
        <div class="xl:pl-56">

            <!-- Header -->
            <header class="sticky top-0 z-10 border-b border-zinc-200 bg-white/90 px-6 py-3 backdrop-blur dark:border-zinc-800 dark:bg-zinc-950/90">
                <div class="flex items-center justify-between gap-4">

                    <!-- Search -->
                    <div class="flex w-72 items-center gap-2 rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm text-zinc-400 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0015.803 15.803z" />
                        </svg>
                        <span>Buscar no IntranetZK...</span>
                        <kbd class="ml-auto rounded border border-zinc-200 px-1.5 py-0.5 text-xs dark:border-zinc-700 dark:text-zinc-600">Ctrl K</kbd>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-1">

                        <!-- Theme -->
                        <button
                            type="button"
                            class="rounded-lg p-2 text-zinc-400 transition-colors hover:bg-zinc-100 hover:text-zinc-700 dark:hover:bg-zinc-800 dark:hover:text-zinc-200"
                            @click="toggleTheme"
                            title="Alternar tema"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                            </svg>
                        </button>

                        <!-- Bell -->
                        <button
                            type="button"
                            class="relative rounded-lg p-2 text-zinc-400 transition-colors hover:bg-zinc-100 hover:text-zinc-700 dark:hover:bg-zinc-800 dark:hover:text-zinc-200"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                            </svg>
                            <span class="absolute right-1.5 top-1.5 h-2 w-2 rounded-full bg-brand-500 ring-2 ring-white dark:ring-zinc-950" />
                        </button>

                        <div class="mx-1.5 h-5 w-px bg-zinc-200 dark:bg-zinc-800" />

                        <!-- User -->
                        <Link
                            href="/meu-perfil"
                            class="flex items-center gap-2.5 rounded-lg px-2 py-1.5 transition-colors hover:bg-zinc-100 dark:hover:bg-zinc-800"
                        >
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-500/15 text-xs font-semibold text-brand-600 dark:text-brand-400">
                                {{ initials(user?.name) }}
                            </div>
                            <div class="hidden text-left sm:block">
                                <p class="text-sm font-medium leading-tight text-zinc-900 dark:text-white">{{ user?.name }}</p>
                                <p class="text-xs capitalize leading-tight text-zinc-400">{{ user?.profile }}</p>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="hidden h-4 w-4 text-zinc-400 sm:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </Link>

                        <button
                            type="button"
                            class="rounded-lg px-3 py-1.5 text-sm text-zinc-400 transition-colors hover:bg-zinc-100 hover:text-zinc-700 dark:hover:bg-zinc-800 dark:hover:text-zinc-200"
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
