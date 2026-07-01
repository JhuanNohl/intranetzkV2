<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { router, useForm } from '@inertiajs/vue3'

const props = defineProps({
    document: {
        type: Object,
        required: true,
    },
    departments: {
        type: Array,
        required: true,
    },
    categories: {
        type: Array,
        required: true,
    },
    sourceTypeOptions: {
        type: Array,
        required: true,
    },
    action: {
        type: String,
        required: true,
    },
    method: {
        type: String,
        default: 'post',
    },
})

const form = useForm({
    department_id: props.document.department_id ?? '',
    document_category_id: props.document.document_category_id ?? '',
    title: props.document.title ?? '',
    summary: props.document.summary ?? '',
    content: props.document.content ?? '',
    source_type: props.document.source_type ?? 'upload',
    document_type: 'other',
    file: null,
    external_url: props.document.external_url ?? '',
    status: props.document.status ?? 'draft',
    visibility: props.document.visibility ?? 'department',
    is_featured: Boolean(props.document.is_featured),
    published_at: props.document.published_at ?? '',
    expires_at: props.document.expires_at ?? '',
    visible_department_ids: [],
})
const allowNavigation = ref(false)
const showUnsavedModal = ref(false)
const pendingVisitUrl = ref(null)
let removeBeforeListener = null

const visibilityOptions = [
    { value: 'department', label: 'Privado' },
    { value: 'public', label: 'Publico' },
]

const filteredCategories = computed(() => {
    if (!form.department_id) return props.categories

    const departmentCategories = props.categories.filter((category) => {
        return Number(category.department_id) === Number(form.department_id)
    })

    if (departmentCategories.length > 0) return departmentCategories

    return props.categories.filter((category) => !category.department_id)
})

watch(() => form.department_id, () => {
    const stillValid = filteredCategories.value.some((category) => Number(category.id) === Number(form.document_category_id))

    if (!stillValid) form.document_category_id = ''
})

function hasUnsavedChanges() {
    return form.isDirty && !form.processing && !allowNavigation.value
}

function submit(status = 'published', afterSuccess = null) {
    form.status = status
    form.visible_department_ids = []
    allowNavigation.value = true

    const options = {
        forceFormData: true,
        preserveScroll: true,
        onStart: () => {
            allowNavigation.value = true
        },
        onError: () => {
            allowNavigation.value = false
        },
        onSuccess: () => {
            allowNavigation.value = true
            afterSuccess?.()
        },
    }

    if (props.method === 'put') {
        form.transform((data) => ({ ...data, _method: 'put' })).post(props.action, options)
        return
    }

    form.post(props.action, options)
}

function setPublishedNow() {
    const now = new Date()
    const offset = now.getTimezoneOffset()
    const local = new Date(now.getTime() - offset * 60000)

    form.published_at = local.toISOString().slice(0, 16)
}

function clearExpiration() {
    form.expires_at = ''
}

function continueEditing() {
    showUnsavedModal.value = false
    pendingVisitUrl.value = null
}

function leaveWithoutSaving() {
    allowNavigation.value = true
    showUnsavedModal.value = false

    if (pendingVisitUrl.value) {
        router.visit(pendingVisitUrl.value)
        return
    }

    window.history.back()
}

function saveDraftAndLeave() {
    const target = pendingVisitUrl.value

    submit('draft', () => {
        if (target) {
            router.visit(target)
            return
        }

        window.history.back()
    })
}

function confirmBeforeUnload(event) {
    if (!hasUnsavedChanges()) return

    event.preventDefault()
    event.returnValue = ''
}

onMounted(() => {
    window.addEventListener('beforeunload', confirmBeforeUnload)
    removeBeforeListener = router.on('before', (event) => {
        if (!hasUnsavedChanges()) return

        event.preventDefault()
        pendingVisitUrl.value = event.detail.visit.url.toString()
        showUnsavedModal.value = true
    })
})

onBeforeUnmount(() => {
    window.removeEventListener('beforeunload', confirmBeforeUnload)
    removeBeforeListener?.()
})
</script>

<template>
    <form class="space-y-6" @submit.prevent="submit('published')">
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-[minmax(0,1fr)_18rem]">
            <section class="space-y-5 rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                <div>
                    <label class="text-sm font-medium text-zinc-800 dark:text-zinc-100" for="title">Titulo</label>
                    <input
                        id="title"
                        v-model="form.title"
                        type="text"
                        class="mt-2 w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm outline-none ring-brand-500/20 transition focus:border-brand-500 focus:ring-4 dark:border-white/10 dark:bg-zinc-950"
                    >
                    <p v-if="form.errors.title" class="mt-1 text-xs text-red-500">{{ form.errors.title }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-zinc-800 dark:text-zinc-100" for="summary">Resumo</label>
                    <textarea
                        id="summary"
                        v-model="form.summary"
                        rows="3"
                        class="mt-2 w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm outline-none ring-brand-500/20 transition focus:border-brand-500 focus:ring-4 dark:border-white/10 dark:bg-zinc-950"
                    />
                    <p v-if="form.errors.summary" class="mt-1 text-xs text-red-500">{{ form.errors.summary }}</p>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium text-zinc-800 dark:text-zinc-100" for="department_id">Departamento</label>
                        <select
                            id="department_id"
                            v-model="form.department_id"
                            class="mt-2 w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm outline-none ring-brand-500/20 transition focus:border-brand-500 focus:ring-4 dark:border-white/10 dark:bg-zinc-950"
                        >
                            <option value="">Sem departamento</option>
                            <option v-for="department in departments" :key="department.id" :value="department.id">
                                {{ department.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.department_id" class="mt-1 text-xs text-red-500">{{ form.errors.department_id }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-zinc-800 dark:text-zinc-100" for="document_category_id">Categoria</label>
                        <select
                            id="document_category_id"
                            v-model="form.document_category_id"
                            class="mt-2 w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm outline-none ring-brand-500/20 transition focus:border-brand-500 focus:ring-4 dark:border-white/10 dark:bg-zinc-950"
                        >
                            <option value="">Sem categoria</option>
                            <option v-for="category in filteredCategories" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.document_category_id" class="mt-1 text-xs text-red-500">{{ form.errors.document_category_id }}</p>
                    </div>
                </div>

                <div class="space-y-4 rounded-lg border border-zinc-200 p-4 dark:border-zinc-800">
                    <div>
                        <select
                            id="source_type"
                            v-model="form.source_type"
                            class="mt-2 w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm outline-none ring-brand-500/20 transition focus:border-brand-500 focus:ring-4 dark:border-white/10 dark:bg-zinc-950"
                        >
                            <option v-for="option in sourceTypeOptions" :key="option.value" :value="option.value">
                                {{ option.label }}
                            </option>
                        </select>
                    </div>

                    <div v-if="form.source_type === 'upload'">
                        <label class="text-sm font-medium text-zinc-800 dark:text-zinc-100" for="file">Arquivo</label>
                        <input
                            id="file"
                            type="file"
                            class="mt-2 block w-full text-sm text-zinc-600 file:mr-4 file:rounded-lg file:border-0 file:bg-brand-600 file:px-3 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-brand-700 dark:text-zinc-300"
                            @input="form.file = $event.target.files[0]"
                        >
                        <p v-if="document.original_filename" class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">Arquivo atual: {{ document.original_filename }}</p>
                        <p v-if="form.errors.file" class="mt-1 text-xs text-red-500">{{ form.errors.file }}</p>
                    </div>

                    <div v-if="form.source_type === 'external'">
                        <label class="text-sm font-medium text-zinc-800 dark:text-zinc-100" for="external_url">Link externo</label>
                        <input
                            id="external_url"
                            v-model="form.external_url"
                            type="url"
                            class="mt-2 w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm outline-none ring-brand-500/20 transition focus:border-brand-500 focus:ring-4 dark:border-white/10 dark:bg-zinc-950"
                        >
                        <p v-if="form.errors.external_url" class="mt-1 text-xs text-red-500">{{ form.errors.external_url }}</p>
                    </div>

                    <div v-if="form.source_type === 'content'">
                        <label class="text-sm font-medium text-zinc-800 dark:text-zinc-100" for="content">Conteudo</label>
                        <textarea
                            id="content"
                            v-model="form.content"
                            rows="10"
                            class="mt-2 w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm outline-none ring-brand-500/20 transition focus:border-brand-500 focus:ring-4 dark:border-white/10 dark:bg-zinc-950"
                        />
                        <p v-if="form.errors.content" class="mt-1 text-xs text-red-500">{{ form.errors.content }}</p>
                    </div>
                </div>
            </section>

            <aside class="space-y-5">
                <section class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-zinc-800 dark:text-zinc-100" for="visibility">Visibilidade</label>
                            <select
                                id="visibility"
                                v-model="form.visibility"
                                class="mt-2 w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm outline-none ring-brand-500/20 transition focus:border-brand-500 focus:ring-4 dark:border-white/10 dark:bg-zinc-950"
                            >
                                <option v-for="option in visibilityOptions" :key="option.value" :value="option.value">
                                    {{ option.label }}
                                </option>
                            </select>
                            <p v-if="form.errors.visibility" class="mt-1 text-xs text-red-500">{{ form.errors.visibility }}</p>
                        </div>

                        <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                            <input v-model="form.is_featured" type="checkbox" class="size-4 rounded border-zinc-300 text-brand-600 focus:ring-brand-500 dark:border-white/20 dark:bg-zinc-950">
                            Destacar arquivo
                        </label>
                    </div>
                </section>

                <section class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-zinc-800 dark:text-zinc-100" for="published_at">Publicacao</label>
                            <button
                                type="button"
                                class="mt-2 inline-flex items-center gap-2 rounded-lg border border-zinc-200 px-3 py-2 text-xs font-medium text-zinc-600 transition hover:bg-zinc-50 dark:border-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-800"
                                @click="setPublishedNow"
                            >
                                <i class="bi bi-clock" aria-hidden="true" />
                                Usar data e hora atual
                            </button>
                            <input
                                id="published_at"
                                v-model="form.published_at"
                                type="datetime-local"
                                class="mt-2 w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm outline-none ring-brand-500/20 transition focus:border-brand-500 focus:ring-4 dark:border-white/10 dark:bg-zinc-950"
                            >
                        </div>

                        <div>
                            <label class="text-sm font-medium text-zinc-800 dark:text-zinc-100" for="expires_at">Expiracao</label>
                            <button
                                type="button"
                                class="mt-2 inline-flex items-center gap-2 rounded-lg border border-zinc-200 px-3 py-2 text-xs font-medium text-zinc-600 transition hover:bg-zinc-50 dark:border-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-800"
                                @click="clearExpiration"
                            >
                                <i class="bi bi-infinity" aria-hidden="true" />
                                Sem data de expiracao
                            </button>
                            <input
                                id="expires_at"
                                v-model="form.expires_at"
                                type="datetime-local"
                                class="mt-2 w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm outline-none ring-brand-500/20 transition focus:border-brand-500 focus:ring-4 dark:border-white/10 dark:bg-zinc-950"
                            >
                            <p v-if="form.errors.expires_at" class="mt-1 text-xs text-red-500">{{ form.errors.expires_at }}</p>
                        </div>
                    </div>
                </section>
            </aside>
        </div>

        <div class="flex flex-wrap items-center justify-end gap-3">
            <Link href="/documents" class="rounded-lg px-4 py-2 text-sm font-medium text-zinc-600 transition hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800" @click="allowNavigation = true">
                Cancelar
            </Link>
            <button
                type="button"
                class="rounded-lg border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 transition hover:bg-zinc-50 disabled:cursor-not-allowed disabled:opacity-60 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-800"
                :disabled="form.processing"
                @click="submit('draft')"
            >
                Salvar rascunho
            </button>
            <button
                type="submit"
                class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-brand-700 disabled:cursor-not-allowed disabled:opacity-60"
                :disabled="form.processing"
            >
                Salvar
            </button>
        </div>

        <div
            v-if="showUnsavedModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/60 px-4"
            role="dialog"
            aria-modal="true"
        >
            <section class="w-full max-w-md rounded-lg border border-zinc-200 bg-white p-5 shadow-xl dark:border-zinc-800 dark:bg-zinc-900">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Salvar rascunho?</h2>
                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                    Existem alteracoes nao salvas neste arquivo. Salve como rascunho, descarte as alteracoes ou continue editando.
                </p>
                <div class="mt-5 flex flex-wrap justify-end gap-2">
                    <button
                        type="button"
                        class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-600 transition hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800"
                        @click="continueEditing"
                    >
                        Continuar editando
                    </button>
                    <button
                        type="button"
                        class="rounded-lg border border-zinc-300 px-3 py-2 text-sm font-medium text-zinc-700 transition hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-800"
                        @click="leaveWithoutSaving"
                    >
                        Descartar
                    </button>
                    <button
                        type="button"
                        class="rounded-lg bg-brand-600 px-3 py-2 text-sm font-medium text-white transition hover:bg-brand-700 disabled:cursor-not-allowed disabled:opacity-60"
                        :disabled="form.processing"
                        @click="saveDraftAndLeave"
                    >
                        Salvar rascunho
                    </button>
                </div>
            </section>
        </div>
    </form>
</template>
