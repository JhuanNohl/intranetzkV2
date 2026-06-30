<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

const groups = [
    {
        items: [
            { label: 'Início', href: '/', route: '/', category: 'Principal', icon: 'bi bi-house-door' },
        ],
    },
    {
        label: 'Corporativo',
        category: 'Corporativo',
        icon: 'bi bi-building',
        items: [
            { label: 'Comercial',            href: '/comercial',            route: '/comercial',            category: 'Corporativo', icon: 'bi bi-briefcase' },
            { label: 'Departamento Pessoal', href: '/departamento-pessoal', route: '/departamento-pessoal', category: 'Corporativo', icon: 'bi bi-people' },
            { label: 'Financeiro',           href: '/financeiro',           route: '/financeiro',           category: 'Corporativo', icon: 'bi bi-currency-dollar' },
        ],
    },
    {
        label: 'Área Técnica',
        category: 'Área Técnica',
        icon: 'bi bi-tools',
        items: [
            { label: 'Desenvolvimento', href: '/desenvolvimento', route: '/desenvolvimento', category: 'Área Técnica', icon: 'bi bi-code-slash' },
            { label: 'Suporte',         href: '/suporte',         route: '/suporte',         category: 'Área Técnica', icon: 'bi bi-headset' },
            { label: 'T.I.',            href: '/ti',              route: '/ti',              category: 'Área Técnica', icon: 'bi bi-display' },
            { label: 'Treinamentos',    href: '/treinamentos',    route: '/treinamentos',    category: 'Área Técnica', icon: 'bi bi-mortarboard' },
        ],
    },
    {
        label: 'Operacional',
        category: 'Operacional',
        icon: 'bi bi-gear',
        items: [
            { label: 'Fábrica',    href: '/fabrica',    route: '/fabrica',    category: 'Operacional', icon: 'bi bi-buildings' },
            { label: 'Manutenção', href: '/manutencao', route: '/manutencao', category: 'Operacional', icon: 'bi bi-wrench-adjustable' },
            { label: 'Produtos',   href: '/produtos',   route: '/produtos',   category: 'Operacional', icon: 'bi bi-box-seam' },
        ],
    },
]

const page = usePage()
const user = computed(() => page.props.auth?.user)
const searchInput = ref(null)
const searchPanel = ref(null)
const globalSearch = ref('')
const searchPreview = ref({ documents: [] })
const searchLoading = ref(false)
const searchOpen = ref(false)
let searchTimer = null
const menuGroups = computed(() => groups.map(group => ({
    ...group,
    items: group.items.map(item => ({
        ...item,
        active: isActive(item.href),
        current: isActive(item.href),
    })),
})))
const searchableItems = computed(() => [
    ...groups.flatMap(group => group.items),
    { label: 'Central de Documentos', href: '/documents', route: '/documents', category: 'Documentos', icon: 'bi bi-book' },
])
const previewMenuItems = computed(() => {
    const term = normalizeSearch(globalSearch.value)
    if (term.length < 2) return []

    return searchableItems.value
        .filter((item) => {
            const label = normalizeSearch(item.label)
            const category = normalizeSearch(item.category)

            return label.includes(term) || category.includes(term)
        })
        .slice(0, 4)
})
const hasSearchPreview = computed(() => {
    return previewMenuItems.value.length > 0 || searchPreview.value.documents.length > 0
})

function normalizeSearch(value) {
    return String(value ?? '')
        .normalize('NFD')
        .replace(/\p{Diacritic}/gu, '')
        .replace(/[^\p{Letter}\p{Number}\s]/gu, '')
        .toLowerCase()
        .trim()
}

function initials(name) {
    if (!name) return 'U'
    return name.split(' ').slice(0, 2).map(n => n[0]).join('').toUpperCase()
}

function isActive(href) {
    if (href === '/') return page.url === '/'
    return page.url === href || page.url.startsWith(href + '/')
}

function submitGlobalSearch() {
    const term = globalSearch.value.trim()
    if (!term) return

    const normalizedTerm = normalizeSearch(term)
    const matchedItem = searchableItems.value.find((item) => {
        const label = normalizeSearch(item.label)
        const category = normalizeSearch(item.category)

        return label === normalizedTerm
            || label.includes(normalizedTerm)
            || normalizedTerm.includes(label)
            || category === normalizedTerm
    })

    if (matchedItem) {
        router.visit(matchedItem.href)
        return
    }

    router.get('/documents', { search: term }, {
        preserveState: false,
        replace: false,
    })
}

function visitSearchResult(href) {
    searchOpen.value = false
    searchPreview.value = { documents: [] }
    router.visit(href)
}

async function loadSearchPreview() {
    const term = globalSearch.value.trim()

    if (term.length < 2) {
        searchPreview.value = { documents: [] }
        searchOpen.value = false
        return
    }

    searchLoading.value = true
    searchOpen.value = true

    try {
        const response = await fetch(`/search/preview?search=${encodeURIComponent(term)}`, {
            headers: {
                Accept: 'application/json',
            },
        })

        if (!response.ok) {
            searchPreview.value = { documents: [] }
            return
        }

        searchPreview.value = await response.json()
    } finally {
        searchLoading.value = false
    }
}

function closeSearchPreview(event) {
    if (!searchPanel.value?.contains(event.target) && event.target !== searchInput.value) {
        searchOpen.value = false
    }
}

function focusGlobalSearch(event) {
    if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') {
        event.preventDefault()
        searchInput.value?.focus()
    }
}

watch(globalSearch, () => {
    clearTimeout(searchTimer)
    searchTimer = setTimeout(loadSearchPreview, 220)
})

onMounted(() => {
    window.addEventListener('keydown', focusGlobalSearch)
    window.addEventListener('click', closeSearchPreview)
})
onBeforeUnmount(() => {
    clearTimeout(searchTimer)
    window.removeEventListener('keydown', focusGlobalSearch)
    window.removeEventListener('click', closeSearchPreview)
})

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
            <div class="flex h-20 items-center border-b border-zinc-200 px-4 dark:border-zinc-800">
                <Link href="/" class="block w-full" aria-label="IntranetZK">
                    <img
                        :src="'/img/logo-zkteco-light.png'"
                        alt="ZKTeco"
                        class="h-8 w-full object-contain object-center dark:hidden"
                    >
                    <img
                        :src="'/img/logo-zkteco-dark.png'"
                        alt="ZKTeco"
                        class="hidden h-8 w-full object-contain object-center dark:block"
                    >
                </Link>
            </div>

            <!-- Nav -->
            <nav class="flex-1 space-y-4 overflow-y-auto px-3 py-4">
                <div v-for="group in menuGroups" :key="group.label ?? '_root'">
                    <p
                        v-if="group.label"
                        class="sidebar-section-title"
                    >
                        <i :class="group.icon" class="sidebar-section-icon" aria-hidden="true" />
                        {{ group.label }}
                    </p>
                    <div class="space-y-0.5">
                        <Link
                            v-for="item in group.items"
                            :key="item.href"
                            :href="item.href"
                            class="sidebar-link"
                            :class="{ active: item.active }"
                            :aria-current="item.current ? 'page' : null"
                        >
                            <i :class="item.icon" class="sidebar-icon" aria-hidden="true" />
                            {{ item.label }}
                        </Link>
                    </div>
                </div>
            </nav>

            <!-- Documents -->
            <div class="border-t border-zinc-200 px-3 py-3 dark:border-zinc-800">
                <p class="sidebar-section-title mb-2">
                    <i class="bi bi-journal-text sidebar-section-icon" aria-hidden="true" />
                    Documentos
                </p>

                <Link
                    href="/documents"
                    class="sidebar-link mt-1"
                    :class="{ active: isActive('/documents') && !isActive('/documents/create') }"
                    :aria-current="isActive('/documents') && !isActive('/documents/create') ? 'page' : null"
                >
                    <i class="bi bi-book sidebar-icon" aria-hidden="true" />
                    Central de Documentos
                </Link>
            </div>
        </aside>

        <!-- Main -->
        <div class="xl:pl-56">

            <!-- Header -->
            <header class="sticky top-0 z-10 border-b border-zinc-200 bg-white/90 px-6 py-3 backdrop-blur dark:border-zinc-800 dark:bg-zinc-950/90">
                <div class="flex items-center justify-between gap-4">

                    <!-- Search -->
                    <form
                        class="relative flex w-72 items-center gap-2 rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm text-zinc-500 transition focus-within:border-brand-500 focus-within:ring-4 focus-within:ring-brand-500/15 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-400"
                        role="search"
                        @submit.prevent="submitGlobalSearch"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0015.803 15.803z" />
                        </svg>
                        <input
                            ref="searchInput"
                            v-model="globalSearch"
                            type="search"
                            class="min-w-0 flex-1 bg-transparent text-sm text-zinc-700 outline-none placeholder:text-zinc-400 dark:text-zinc-200 dark:placeholder:text-zinc-500"
                            placeholder="Buscar no IntranetZK..."
                            aria-label="Buscar documentos ou setores"
                            @focus="globalSearch.trim().length >= 2 && (searchOpen = true)"
                            @keydown.escape="searchOpen = false"
                        >
                        <kbd class="ml-auto rounded border border-zinc-200 px-1.5 py-0.5 text-xs dark:border-zinc-700 dark:text-zinc-600">Ctrl K</kbd>

                        <div
                            v-if="searchOpen"
                            ref="searchPanel"
                            class="absolute left-0 top-full z-30 mt-2 w-[30rem] max-w-[calc(100vw-2rem)] overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-xl dark:border-zinc-800 dark:bg-zinc-900"
                        >
                            <div v-if="searchLoading" class="px-4 py-3 text-sm text-zinc-500 dark:text-zinc-400">
                                Buscando...
                            </div>

                            <div v-else-if="hasSearchPreview" class="max-h-96 overflow-y-auto py-2">
                                <div v-if="searchPreview.documents.length" class="px-2">
                                    <p class="px-2 py-1 text-[10px] font-semibold uppercase tracking-widest text-brand-600 dark:text-brand-400">
                                        Documentos encontrados
                                    </p>
                                    <button
                                        v-for="document in searchPreview.documents"
                                        :key="document.id"
                                        type="button"
                                        class="group flex w-full items-start gap-3 rounded-lg px-2 py-2 text-left transition-all hover:-translate-y-0.5 hover:bg-brand-500/5 hover:shadow-sm active:translate-y-0 dark:hover:bg-brand-500/10"
                                        @click="visitSearchResult(document.href)"
                                    >
                                        <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-brand-500/10 text-brand-700 transition-transform group-hover:scale-105 dark:text-brand-400">
                                            <i class="bi bi-file-earmark-text text-lg leading-none" aria-hidden="true" />
                                        </span>
                                        <span class="min-w-0 flex-1">
                                            <span class="block truncate text-sm font-medium text-zinc-900 transition-colors group-hover:text-brand-700 dark:text-zinc-100 dark:group-hover:text-brand-400">{{ document.title }}</span>
                                            <span class="mt-0.5 block truncate text-xs text-zinc-500 dark:text-zinc-400">
                                                {{ document.department }}<template v-if="document.category"> / {{ document.category }}</template>
                                            </span>
                                        </span>
                                    </button>
                                </div>

                                <div v-if="previewMenuItems.length" class="mt-2 border-t border-zinc-100 px-2 pt-2 dark:border-zinc-800">
                                    <p class="px-2 py-1 text-[10px] font-semibold uppercase tracking-widest text-zinc-400">
                                        Setores e atalhos
                                    </p>
                                    <button
                                        v-for="item in previewMenuItems"
                                        :key="item.href"
                                        type="button"
                                        class="group flex w-full items-center gap-3 rounded-lg px-2 py-2 text-left transition-all hover:-translate-y-0.5 hover:bg-zinc-100 hover:shadow-sm active:translate-y-0 dark:hover:bg-zinc-800"
                                        @click="visitSearchResult(item.href)"
                                    >
                                        <i :class="item.icon" class="w-5 shrink-0 text-center text-zinc-500 transition-colors group-hover:text-brand-700 dark:text-zinc-400 dark:group-hover:text-brand-400" aria-hidden="true" />
                                        <span class="min-w-0">
                                            <span class="block truncate text-sm font-medium text-zinc-900 transition-colors group-hover:text-brand-700 dark:text-zinc-100 dark:group-hover:text-brand-400">{{ item.label }}</span>
                                            <span class="block truncate text-xs text-zinc-500 dark:text-zinc-400">{{ item.category }}</span>
                                        </span>
                                    </button>
                                </div>
                            </div>

                            <div v-else class="px-4 py-3 text-sm text-zinc-500 dark:text-zinc-400">
                                Nenhum documento ou setor encontrado.
                            </div>
                        </div>
                    </form>

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
