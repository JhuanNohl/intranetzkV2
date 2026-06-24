<script setup>
import AppShell from '@/Layouts/AppShell.vue'
import DocumentForm from './DocumentForm.vue'

defineOptions({ layout: AppShell })

defineProps({
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
    statusOptions: {
        type: Array,
        required: true,
    },
    sourceTypeOptions: {
        type: Array,
        required: true,
    },
    documentTypeOptions: {
        type: Array,
        required: true,
    },
    visibilityOptions: {
        type: Array,
        required: true,
    },
})
</script>

<template>
    <Head title="Novo documento" />

    <div class="space-y-6">
        <div>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Documentos</p>
            <h1 class="mt-1 text-2xl font-semibold tracking-tight">Novo documento</h1>
        </div>

        <DocumentForm
            :document="document"
            :departments="departments"
            :categories="categories"
            :status-options="statusOptions"
            :source-type-options="sourceTypeOptions"
            :document-type-options="documentTypeOptions"
            :visibility-options="visibilityOptions"
            action="/documents"
            submit-label="Criar documento"
        />
    </div>
</template>
