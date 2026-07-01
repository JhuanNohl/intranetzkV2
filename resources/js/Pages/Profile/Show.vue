<script setup>
import AppShell from '@/Layouts/AppShell.vue'
import { router, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import AvatarUploadModal from './Partials/AvatarUploadModal.vue'

defineOptions({
    layout: AppShell,
})

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
})

const avatarModalOpen = ref(false)
const avatarForm = useForm({
    avatar: null,
})

const profileForm = useForm({
    name: props.user.name ?? '',
    position: props.user.position ?? '',
    phone: props.user.phone ?? '',
    extension: props.user.extension ?? '',
})

function initials(name) {
    if (!name) return 'U'
    return name.split(' ').slice(0, 2).map(n => n[0]).join('').toUpperCase()
}

function onCroppedAvatar(file) {
    avatarForm.avatar = file
    avatarForm.post('/meu-perfil/foto', {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            avatarModalOpen.value = false
        },
        onFinish: () => {
            avatarForm.reset()
        },
    })
}

function removeAvatar() {
    if (!confirm('Remover a foto de perfil?')) return

    router.delete('/meu-perfil/foto', { preserveScroll: true })
}

function saveProfile() {
    profileForm.put('/meu-perfil', { preserveScroll: true })
}
</script>

<template>
    <Head title="Meu perfil" />

    <section class="space-y-6">
        <div>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Conta</p>
            <h1 class="text-2xl font-semibold tracking-tight">Meu perfil</h1>
        </div>

        <div class="max-w-2xl rounded-xl border border-zinc-200 bg-white p-5 dark:border-white/10 dark:bg-zinc-900">
            <div class="flex items-center gap-4">
                <img
                    v-if="user.avatar_url"
                    :src="user.avatar_url"
                    alt=""
                    class="h-20 w-20 shrink-0 rounded-full object-cover"
                >
                <div v-else class="flex h-20 w-20 shrink-0 items-center justify-center rounded-full bg-brand-500/15 text-2xl font-semibold text-brand-600 dark:text-brand-400">
                    {{ initials(user.name) }}
                </div>

                <div class="space-y-2">
                    <div class="flex flex-wrap gap-2">
                        <button
                            type="button"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-3 py-1.5 text-sm font-medium text-white transition hover:bg-brand-700"
                            @click="avatarModalOpen = true"
                        >
                            <i class="bi bi-camera-fill text-sm leading-none" aria-hidden="true" />
                            Alterar foto
                        </button>
                        <button
                            v-if="user.avatar_url"
                            type="button"
                            class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-sm font-medium text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10"
                            @click="removeAvatar"
                        >
                            Remover foto
                        </button>
                    </div>
                    <p class="text-xs text-zinc-400 dark:text-zinc-500">JPG ou PNG, até 4MB.</p>
                </div>
            </div>
        </div>

        <form class="max-w-2xl overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-white/10 dark:bg-zinc-900" @submit.prevent="saveProfile">
            <dl class="divide-y divide-zinc-200 dark:divide-white/10">
                <div class="grid gap-1 px-5 py-4 sm:grid-cols-3 sm:gap-4 sm:items-center">
                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Nome</dt>
                    <dd class="sm:col-span-2">
                        <input
                            v-model="profileForm.name"
                            type="text"
                            class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-1.5 text-sm outline-none ring-brand-500/20 transition focus:border-brand-500 focus:ring-4 dark:border-white/10 dark:bg-zinc-950"
                        >
                        <p v-if="profileForm.errors.name" class="mt-1 text-xs font-medium text-red-600 dark:text-red-400">{{ profileForm.errors.name }}</p>
                    </dd>
                </div>

                <div class="grid gap-1 px-5 py-4 sm:grid-cols-3 sm:gap-4 sm:items-center">
                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">E-mail</dt>
                    <dd class="text-sm sm:col-span-2">{{ user.email }}</dd>
                </div>

                <div class="grid gap-1 px-5 py-4 sm:grid-cols-3 sm:gap-4 sm:items-center">
                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Perfil</dt>
                    <dd class="text-sm font-medium capitalize sm:col-span-2">{{ user.profile }}</dd>
                </div>

                <div class="grid gap-1 px-5 py-4 sm:grid-cols-3 sm:gap-4 sm:items-center">
                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Setor</dt>
                    <dd class="text-sm sm:col-span-2">{{ user.department ?? '-' }}</dd>
                </div>

                <div class="grid gap-1 px-5 py-4 sm:grid-cols-3 sm:gap-4 sm:items-center">
                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Cargo</dt>
                    <dd class="sm:col-span-2">
                        <input
                            v-model="profileForm.position"
                            type="text"
                            class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-1.5 text-sm outline-none ring-brand-500/20 transition focus:border-brand-500 focus:ring-4 dark:border-white/10 dark:bg-zinc-950"
                        >
                    </dd>
                </div>

                <div class="grid gap-1 px-5 py-4 sm:grid-cols-3 sm:gap-4 sm:items-center">
                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Telefone</dt>
                    <dd class="sm:col-span-2">
                        <input
                            v-model="profileForm.phone"
                            type="text"
                            class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-1.5 text-sm outline-none ring-brand-500/20 transition focus:border-brand-500 focus:ring-4 dark:border-white/10 dark:bg-zinc-950"
                        >
                    </dd>
                </div>

                <div class="grid gap-1 px-5 py-4 sm:grid-cols-3 sm:gap-4 sm:items-center">
                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Ramal</dt>
                    <dd class="sm:col-span-2">
                        <input
                            v-model="profileForm.extension"
                            type="text"
                            class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-1.5 text-sm outline-none ring-brand-500/20 transition focus:border-brand-500 focus:ring-4 dark:border-white/10 dark:bg-zinc-950"
                        >
                    </dd>
                </div>

                <div class="grid gap-1 px-5 py-4 sm:grid-cols-3 sm:gap-4 sm:items-center">
                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Último acesso</dt>
                    <dd class="text-sm sm:col-span-2">{{ user.last_login_at ?? '-' }}</dd>
                </div>
            </dl>

            <div class="flex items-center justify-end gap-3 border-t border-zinc-200 px-5 py-4 dark:border-white/10">
                <p v-if="profileForm.recentlySuccessful" class="text-sm font-medium text-emerald-600 dark:text-emerald-400">Alterações salvas.</p>
                <button
                    type="submit"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-brand-700 disabled:cursor-not-allowed disabled:opacity-60"
                    :disabled="profileForm.processing"
                >
                    {{ profileForm.processing ? 'Salvando...' : 'Salvar alterações' }}
                </button>
            </div>
        </form>

        <AvatarUploadModal
            v-model:open="avatarModalOpen"
            :processing="avatarForm.processing"
            @save="onCroppedAvatar"
        />
    </section>
</template>
