<script setup>
const open = defineModel('open', { type: Boolean, default: false })

defineProps({
    title: {
        type: String,
        default: 'Confirmar ação',
    },
    message: {
        type: String,
        default: 'Tem certeza que deseja continuar?',
    },
    confirmLabel: {
        type: String,
        default: 'Confirmar',
    },
    cancelLabel: {
        type: String,
        default: 'Cancelar',
    },
    processing: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['confirm'])

function close() {
    open.value = false
}

function confirm() {
    emit('confirm')
}
</script>

<template>
    <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="close">
        <div class="w-full max-w-sm rounded-xl border border-zinc-200 bg-white p-5 shadow-2xl dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex items-start gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-500/10 text-red-600 dark:text-red-400">
                    <i class="bi bi-exclamation-triangle-fill text-lg leading-none" aria-hidden="true" />
                </div>
                <div class="min-w-0">
                    <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">{{ title }}</h2>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ message }}</p>
                </div>
            </div>

            <div class="mt-5 flex justify-end gap-2">
                <button
                    type="button"
                    class="rounded-lg px-4 py-2 text-sm font-medium text-zinc-600 transition hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800"
                    :disabled="processing"
                    @click="close"
                >
                    {{ cancelLabel }}
                </button>
                <button
                    type="button"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-60"
                    :disabled="processing"
                    @click="confirm"
                >
                    {{ processing ? 'Removendo...' : confirmLabel }}
                </button>
            </div>
        </div>
    </div>
</template>
