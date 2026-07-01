<script setup>
import AppShell from '@/Layouts/AppShell.vue'
import { ref } from 'vue'

defineOptions({ layout: AppShell })

const infoCards = [
    {
        title: 'Microsoft Teams',
        description: 'Comunicação Interna da Equipe',
        action: 'Dicas e Recursos',
        icon: 'bi bi-chat-square-dots',
        modalTitle: 'Dicas sobre o Microsoft Teams',
        modalSections: [
            {
                type: 'list',
                items: [
                    { icon: 'bi bi-chat-dots-fill', text: 'Utilize canais e equipes para organizar assuntos;' },
                    { icon: 'bi bi-bell-fill', text: 'Utilize @menções para mencionar outros usuários;' },
                    { icon: 'bi bi-telephone-fill', text: 'Realize reuniões e chamadas ilimitadas;' },
                    { icon: 'bi bi-file-earmark-text', text: 'Compartilhe e edite arquivos direto no Teams;' },
                    { icon: 'bi bi-sliders', text: 'Ajuste suas notificações conforme necessário;' },
                ],
            },
        ],
    },
    {
        title: 'Telefonia SIP',
        description: 'Chamadas Corporativas',
        action: 'Saiba mais',
        icon: 'bi bi-telephone',
        modalTitle: 'Telefonia Corporativa - Servidor SIP',
        modalSections: [
            { type: 'heading', text: 'O que é um Servidor SIP?' },
            { type: 'paragraph', text: 'Um servidor SIP (Session Initiation Protocol) é o componente central em sistemas VoIP e de comunicação unificada que gerencia, estabelece e encerra sessões de voz, vídeo e mensagens. Ele atua como um "proxy" ou "registrador", rastreando dispositivos (ramais) e roteando chamadas entre eles. Essencial para PABX IP, ele converte dados digitais em chamadas.' },
            { type: 'section-title', icon: 'bi bi-star-fill', text: 'Principais Características e Funções:' },
            {
                type: 'list',
                items: [
                    { icon: 'bi bi-telephone-outbound-fill', label: 'Gerenciamento de Chamadas:', labelColor: 'text-brand-600 dark:text-brand-400', text: 'Processa mensagens SIP para iniciar e finalizar chamadas;', textColor: 'text-brand-600 dark:text-brand-400' },
                    { icon: 'bi bi-person-badge-fill', label: 'Registro de Usuários:', text: 'Armazena a localização e endereço IP de cada dispositivo;' },
                    { icon: 'bi bi-arrow-left-right', label: 'Proxy e Redirecionamento:', text: 'Funciona como intermediário que roteia conexões;' },
                    { icon: 'bi bi-shield-lock-fill', label: 'Segurança e Sinalização:', text: 'Utiliza UDP, TCP e TLS para maior segurança.' },
                ],
            },
            { type: 'section-title', icon: 'bi bi-lightbulb-fill', text: 'Exemplos de Uso:' },
            {
                type: 'list',
                items: [
                    { icon: 'bi bi-grid-3x3-gap-fill', label: 'PABX IP/Cloud:', labelColor: 'text-brand-600 dark:text-brand-400', text: 'Centrais telefônicas virtuais ou físicas que utilizam SIP;', textColor: 'text-brand-600 dark:text-brand-400' },
                    { icon: 'bi bi-person-video3', label: 'Comunicação de Portaria:', labelColor: 'text-brand-600 dark:text-brand-400', text: 'Integrado a terminais de vídeo portaria e controladores de acesso;', textColor: 'text-brand-600 dark:text-brand-400' },
                    { icon: 'bi bi-arrows-expand', label: 'SIP Trunking:', text: 'Conexão de serviços de voz de operadoras à infraestrutura PABX.' },
                ],
            },
            { type: 'info-note', text: 'Os servidores SIP são cruciais para a telefonia moderna, permitindo a comunicação de áudio e vídeo de forma eficiente e flexível, utilizando a estrutura de dados IP.' },
        ],
    },
    {
        title: 'Acesso Remoto',
        description: 'Suporte Remoto',
        action: 'Informações',
        icon: 'bi bi-laptop',
        modalTitle: 'Acesso Remoto - Teamviewer',
        modalSections: [
            { type: 'paragraph', text: 'Teamviewer é a solução utilizada para suporte remoto e acesso seguro aos computadores da empresa.' },
            {
                type: 'list',
                items: [
                    { icon: 'bi bi-check-circle-fill', iconColor: 'text-green-600 dark:text-green-500', text: 'Acesso remoto seguro a qualquer hora;' },
                    { icon: 'bi bi-check-circle-fill', iconColor: 'text-green-600 dark:text-green-500', text: 'Suporte técnico direto do setor de T.I.;' },
                    { icon: 'bi bi-check-circle-fill', iconColor: 'text-green-600 dark:text-green-500', text: 'Conexão segura com criptografia end-to-end;' },
                    { icon: 'bi bi-info-circle-fill', iconColor: 'text-blue-500 dark:text-blue-400', text: 'Para suporte, entre em contato com o setor de T.I..' },
                ],
            },
            { type: 'download-box', text: 'Você pode baixar a versão mais recente do Teamviewer no link abaixo.', href: '#' },
        ],
        modalFooter: {
            downloadHref: 'https://www.teamviewer.com/pt-br/download/windows/',
            downloadText: 'Baixar Teamviewer',
            downloadIcon: 'bi bi-cloud-arrow-down-fill',
        },
    },
]

const sankhyaLinks = [
    {
        title: 'Sankhya® ERP - Produção',
        description: 'Acesso direto ao sistema',
        href: 'http://zkteco.snk.ativy.com:40049/mge/',
        icon: 'bi bi-link-45deg',
        actionIcon: 'bi bi-box-arrow-up-right',
    },
    {
        title: 'Navegador Sankhya®',
        description: 'Download e acesso ao portal',
        href: 'https://login.sankhya.com.br/?redirect_to=http://downloads.sankhya.com.br/auth/callback.php&application_id=28',
        icon: 'bi bi-download',
        actionIcon: 'bi bi-box-arrow-up-right',
    },
]

const activeModal = ref(null)
const cards = infoCards

function isConfigured(href) {
    return href && href !== '#'
}

function openInfoModal(card) {
    activeModal.value = card
}

function closeModal() {
    activeModal.value = null
}
</script>

<template>
    <Head title="Tecnologia da Informação" />

    <div class="space-y-6">
        <div>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Área Técnica</p>
            <h1 class="mt-1 text-2xl font-semibold tracking-tight">Tecnologia da Informação</h1>
        </div>

        <section class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
            <component
                :is="card.href ? 'a' : 'button'"
                v-for="card in cards"
                :key="card.title"
                :href="card.href"
                :target="isConfigured(card.href) ? '_blank' : null"
                :rel="isConfigured(card.href) ? 'noopener noreferrer' : null"
                :type="card.href ? null : 'button'"
                class="group flex min-h-56 flex-col items-center justify-center rounded-lg border border-zinc-200 bg-white px-6 py-7 text-center shadow-sm transition-colors hover:border-brand-500/50 hover:bg-brand-500/5 dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-brand-500/40 dark:hover:bg-brand-500/10"
                :class="card.href && !isConfigured(card.href) ? 'cursor-default' : ''"
                @click="card.href ? (!isConfigured(card.href) && $event.preventDefault()) : openInfoModal(card)"
            >
                <div class="flex h-20 w-20 items-center justify-center rounded-full bg-brand-500/10 text-brand-700 transition-colors group-hover:bg-brand-500/15 dark:text-brand-400">
                    <i :class="card.icon" class="text-4xl leading-none" aria-hidden="true" />
                </div>
                <h2 class="mt-5 text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ card.title }}</h2>
                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ card.description }}</p>
                <span class="mt-5 inline-flex items-center gap-1.5 rounded-md border border-brand-600 px-3 py-1.5 text-sm font-medium text-brand-700 transition-colors group-hover:bg-brand-600 group-hover:text-white dark:border-brand-500 dark:text-brand-400 dark:group-hover:text-white">
                    <i :class="card.href ? 'bi bi-box-arrow-up-right' : 'bi bi-info-circle'" class="text-[0.8rem] leading-none" aria-hidden="true" />
                    {{ card.action }}
                </span>
            </component>
        </section>

        <section class="overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <header class="flex items-center gap-2 border-b border-zinc-200 px-4 py-3 dark:border-zinc-800">
                <i class="bi bi-browser-chrome text-xl leading-none text-brand-700 dark:text-brand-400" aria-hidden="true" />
                <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Sankhya® ERP</h2>
            </header>

            <div class="grid grid-cols-1 divide-y divide-zinc-200 px-4 dark:divide-zinc-800 lg:grid-cols-2 lg:divide-x lg:divide-y-0">
                <a
                    v-for="link in sankhyaLinks"
                    :key="link.title"
                    :href="link.href"
                    :target="isConfigured(link.href) ? '_blank' : null"
                    :rel="isConfigured(link.href) ? 'noopener noreferrer' : null"
                    class="group grid min-h-20 grid-cols-[2.5rem_minmax(0,1fr)_auto] items-center gap-3 py-4 transition-colors hover:bg-brand-500/5 lg:px-4 dark:hover:bg-brand-500/10"
                    :class="!isConfigured(link.href) ? 'cursor-default' : ''"
                    @click="!isConfigured(link.href) && $event.preventDefault()"
                >
                    <div class="flex h-9 w-9 items-center justify-center text-brand-700 dark:text-brand-400">
                        <i :class="link.icon" class="text-xl leading-none" aria-hidden="true" />
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ link.title }}</p>
                        <p class="mt-1 truncate text-sm text-zinc-500 dark:text-zinc-400">{{ link.description }}</p>
                    </div>
                    <i :class="link.actionIcon" class="text-lg leading-none text-brand-700 dark:text-brand-400" aria-hidden="true" />
                </a>
            </div>
        </section>

        <div
            v-if="activeModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/60 px-4 py-6"
            role="dialog"
            aria-modal="true"
            @click.self="closeModal"
        >
            <section class="flex w-full max-w-lg flex-col overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-xl dark:border-zinc-800 dark:bg-zinc-900" style="max-height: 90vh;">
                <header class="flex shrink-0 items-center justify-between gap-4 border-b border-zinc-200 px-5 py-4 dark:border-zinc-800">
                    <div class="flex min-w-0 items-center gap-3">
                        <i :class="activeModal.icon" class="text-2xl leading-none text-brand-700 dark:text-brand-400" aria-hidden="true" />
                        <h2 class="truncate text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ activeModal.modalTitle }}</h2>
                    </div>
                    <button
                        type="button"
                        class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-zinc-500 transition-colors hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100"
                        aria-label="Fechar"
                        @click="closeModal"
                    >
                        <i class="bi bi-x-lg text-sm leading-none" aria-hidden="true" />
                    </button>
                </header>

                <div class="grow space-y-4 overflow-y-auto px-5 py-5 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                    <template v-for="(section, i) in activeModal.modalSections" :key="i">
                        <p v-if="section.type === 'paragraph'">{{ section.text }}</p>

                        <h3
                            v-else-if="section.type === 'heading'"
                            class="font-semibold text-zinc-900 dark:text-zinc-100"
                        >
                            {{ section.text }}
                        </h3>

                        <div
                            v-else-if="section.type === 'section-title'"
                            class="flex items-center gap-2 font-semibold text-zinc-800 dark:text-zinc-200"
                        >
                            <i :class="[section.icon, 'text-brand-600 dark:text-brand-400']" class="text-base leading-none" aria-hidden="true" />
                            {{ section.text }}
                        </div>

                        <ul v-else-if="section.type === 'list'" class="space-y-0 divide-y divide-zinc-100 dark:divide-zinc-800">
                            <li
                                v-for="(item, j) in section.items"
                                :key="j"
                                class="flex items-start gap-3 py-2.5 first:pt-0 last:pb-0"
                            >
                                <i
                                    :class="[item.icon, item.iconColor || 'text-brand-600 dark:text-brand-400']"
                                    class="mt-0.5 shrink-0 text-base leading-none"
                                    aria-hidden="true"
                                />
                                <span>
                                    <span
                                        v-if="item.label"
                                        :class="['font-semibold', item.labelColor || 'text-zinc-800 dark:text-zinc-100']"
                                    >{{ item.label }} </span>
                                    <span :class="item.textColor">{{ item.text }}</span>
                                </span>
                            </li>
                        </ul>

                        <div
                            v-else-if="section.type === 'info-note'"
                            class="flex items-start gap-2 rounded-md bg-blue-50 px-3 py-2.5 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300"
                        >
                            <i class="bi bi-info-circle-fill mt-0.5 shrink-0 text-sm leading-none" aria-hidden="true" />
                            <p>{{ section.text }}</p>
                        </div>

                        <div
                            v-else-if="section.type === 'download-box'"
                            class="flex items-start gap-2 rounded-md bg-brand-50 px-3 py-2.5 text-brand-700 dark:bg-brand-950/40 dark:text-brand-300"
                        >
                            <i class="bi bi-cloud-arrow-down mt-0.5 shrink-0 text-base leading-none" aria-hidden="true" />
                            <p><span class="font-semibold">Download:</span> {{ section.text }}</p>
                        </div>
                    </template>
                </div>

                <footer class="flex shrink-0 flex-row-reverse items-center gap-3 border-t border-zinc-200 px-5 py-4 dark:border-zinc-800">
                    <button
                        type="button"
                        class="inline-flex items-center rounded-lg border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-800"
                        @click="closeModal"
                    >
                        Fechar
                    </button>
                    <a
                        v-if="activeModal.modalFooter?.downloadHref"
                        :href="activeModal.modalFooter.downloadHref"
                        :target="isConfigured(activeModal.modalFooter.downloadHref) ? '_blank' : null"
                        :rel="isConfigured(activeModal.modalFooter.downloadHref) ? 'noopener noreferrer' : null"
                        class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-brand-700"
                    >
                        <i :class="activeModal.modalFooter.downloadIcon" class="text-sm leading-none" aria-hidden="true" />
                        {{ activeModal.modalFooter.downloadText }}
                    </a>
                </footer>
            </section>
        </div>
    </div>
</template>
