<script setup>
import AppShell from '@/Layouts/AppShell.vue'
import DocumentsIndex from '@/Pages/Documents/Index.vue'

defineOptions({
    layout: AppShell,
    inheritAttrs: false,
})
</script>

<template>
    <DocumentsIndex v-bind="$attrs" />
</template>
