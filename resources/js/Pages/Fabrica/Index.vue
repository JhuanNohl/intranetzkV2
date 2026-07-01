<script setup>
import AppShell from '@/Layouts/AppShell.vue'

defineOptions({ layout: AppShell })

const typeStyles = {
    info: { border: 'border-t-sky-500 dark:border-t-sky-500', badge: 'bg-sky-500', dot: 'bg-sky-500' },
    physical: { border: 'border-t-orange-500 dark:border-t-orange-500', badge: 'bg-orange-500', dot: 'bg-orange-500' },
    erp: { border: 'border-t-brand-500 dark:border-t-brand-500', badge: 'bg-brand-600', dot: 'bg-brand-600' },
    exception: { border: 'border-t-red-500 dark:border-t-red-500', badge: 'bg-red-500', dot: 'bg-red-500' },
}

const legend = [
    { type: 'info', label: 'Fluxo de informações' },
    { type: 'physical', label: 'Fluxo físico / produção' },
    { type: 'erp', label: 'Apontamentos e rastreabilidade no ERP Sankhya' },
    { type: 'exception', label: 'Exceções, devoluções e refugo' },
]

const lanes = [
    {
        title: '1. Planejamento e Suprimentos',
        subtitle: 'Demanda → compras → importação',
        steps: [
            { number: 'A', type: 'info', title: 'Previsão de demanda', description: 'Comercial, compras e estoque avaliam previsão de vendas, projetos e histórico para estimar necessidade de produtos.', note: 'Entrada: demanda comercial' },
            { number: 'B', type: 'info', title: 'Planejamento de estoques', description: 'Análise de produtos disponíveis, itens em trânsito e itens em fabricação pelo HQ para definir quantidades a importar.', note: 'Base: disponibilidade + necessidade' },
            { number: 'C', type: 'info', title: 'Compras e processamento', description: 'Compra de matérias-primas, SKD e produtos acabados, com planejamento do pedido junto aos setores envolvidos.', note: 'Saída: pedido de compra' },
            { number: 'D', type: 'physical', title: 'Transporte internacional', description: 'Envio marítimo ou aéreo, definido principalmente pelo lead time necessário para atendimento da operação.', note: 'Modal: marítimo ou aéreo' },
        ],
    },
    {
        title: '2. Recebimento, Estoque e Rastreabilidade',
        subtitle: 'Conferência → armazenagem → ERP',
        steps: [
            { number: '1', type: 'physical', title: 'Recebimento dos materiais', description: 'Conferência de quantidade, tipo, tamanho, integridade e especificações dos produtos recebidos.', note: 'Controle: pedido x recebido' },
            { number: '2', type: 'physical', title: 'Armazenagem', description: 'Direcionamento para locais pré-definidos no estoque, usando lógica de grupos e endereços definidos no ERP.', note: 'Locais: estoque, produção, expedição etc.' },
            { number: 'ERP', type: 'erp', title: 'Apontamento no Sankhya', description: 'Registro de movimentações e uso de código serial para rastreabilidade, identificação e localização dos produtos.', note: 'Rastreio: código serial' },
            { number: 'G', type: 'info', title: 'Classificação por grupo', description: 'Separação por famílias como industrializados, revenda, SKD, CKD, serviços, despesas e remessa para conserto.', note: 'Critério: grupo de produtos' },
        ],
    },
    {
        title: '3. PCP, Pedidos e Produção',
        subtitle: 'Priorização diária → produção/testes → destino',
        steps: [
            { number: '3', type: 'info', title: 'Processamento de pedidos', description: 'PCP diário define a sequência de atendimento, considerando prioridades, características dos produtos e capacidade da equipe.', note: 'Decisão: expedição direta ou produção/testes' },
            { number: '4', type: 'physical', title: 'Produção e testes', description: 'Matérias-primas e SKDs passam por manufatura e testes para transformação em produtos finalizados.', note: 'Gatilhos: pedido ou estoque' },
            { number: '4.1', type: 'physical', title: 'Movimentação para linha', description: 'Materiais são deslocados da armazenagem para a linha de produção no momento e quantidade adequados.', note: 'Controle: tempo + quantidade' },
            { number: '4.2', type: 'erp', title: 'Apontamentos de produção', description: 'Etapas executadas no ERP Sankhya para controle, rastreamento e verificação do que está sendo produzido.', note: 'Registro: execução da produção' },
            { number: '!', type: 'exception', title: 'Refugo / defeitos sem conserto', description: 'Produtos sem possibilidade de reparo são armazenados em local de refugo e podem servir como doadores de peças.', note: 'Destino: coleta especializada' },
        ],
    },
    {
        title: '4. Expedição, Transporte e Pós-processo',
        subtitle: 'Embalagem → NF → coleta → entrega → retorno',
        steps: [
            { number: '5', type: 'physical', title: 'Conferência e embalagem', description: 'Após produção, testes e apontamentos, os produtos são embalados para proteção e correta identificação.', note: 'Saída: item pronto para NF' },
            { number: 'NF', type: 'erp', title: 'Emissão de nota fiscal', description: 'Após conferência, é realizada a emissão da NF e atualização das informações necessárias para expedição.', note: 'Controle: documentação fiscal' },
            { number: '6', type: 'physical', title: 'Transporte', description: 'Frete CIF ou FOB conforme escolha do cliente, considerando distância, peso, volume, prazo, custo e segurança.', note: 'Coleta: transportadora acionada' },
            { number: '7', type: 'info', title: 'Monitoramento', description: 'Expedição acompanha transporte e entrega por controles internos, garantindo recuperação rápida dos dados.', note: 'Objetivo: conformidade e rastreio' },
            { number: '8', type: 'exception', title: 'Devoluções e RMA', description: 'Logística gerencia devoluções, trocas e manutenções; RMAs seguem para manutenção e retornam pelo fluxo de expedição.', note: 'Destino: manutenção ou estoque' },
            { number: '9', type: 'physical', title: 'Limpeza das áreas', description: 'Áreas produtivas são limpas conforme necessidade; áreas de estoque têm limpeza trimestral por empresa terceirizada.', note: 'Rotina: interna e terceirizada' },
        ],
    },
]

const decisions = [
    {
        title: 'Regras de decisão do processo produtivo',
        items: [
            'Atendimento de pedido possui prioridade sobre produção para estoque.',
            'O PCP diário define sequência com base em prioridade, dificuldade operacional e disponibilidade da equipe.',
            'Pedidos podem seguir diretamente para expedição ou passar por produção e/ou testes.',
        ],
    },
    {
        title: 'Tratamento de devoluções, RMA e avarias',
        items: [
            'RMA recebido é direcionado ao setor de manutenção e depois retorna ao cliente pelo fluxo de expedição.',
            'Devoluções de compras/empréstimos passam por avaliação da manutenção.',
            'Produtos em bom estado retornam ao estoque principal; itens com pequenos defeitos seguem para estoque específico.',
        ],
    },
]
</script>

<template>
    <Head title="Fábrica" />

    <div class="space-y-6">
        <div>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Área Operacional</p>
            <h1 class="mt-1 text-2xl font-semibold tracking-tight">Fábrica</h1>
        </div>

        <section class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-brand-700 dark:text-brand-400">
                Macroprocesso Indústria ZKTeco BR
            </span>
            <h2 class="mt-3 text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">
                Fluxograma do Processo de Produção, Logística e Expedição
            </h2>

            <div class="mt-4 flex flex-wrap gap-2">
                <span
                    v-for="item in legend"
                    :key="item.label"
                    class="inline-flex items-center gap-2 rounded-lg border border-zinc-200 px-3 py-1.5 text-xs text-zinc-600 dark:border-zinc-800 dark:text-zinc-300"
                >
                    <span class="h-2.5 w-2.5 rounded-full" :class="typeStyles[item.type].dot" />
                    {{ item.label }}
                </span>
            </div>
        </section>

        <section
            v-for="lane in lanes"
            :key="lane.title"
            class="overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900"
        >
            <header class="flex flex-wrap items-center justify-between gap-2 border-b border-zinc-200 px-5 py-4 dark:border-zinc-800">
                <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">{{ lane.title }}</h3>
                <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ lane.subtitle }}</span>
            </header>

            <div class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <article
                    v-for="step in lane.steps"
                    :key="lane.title + step.number"
                    class="flex flex-col gap-2.5 rounded-lg border-b border-l border-r border-t-4 border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-800 dark:bg-zinc-950/40"
                    :class="typeStyles[step.type].border"
                >
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg text-sm font-bold text-white" :class="typeStyles[step.type].badge">
                        {{ step.number }}
                    </div>
                    <h4 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ step.title }}</h4>
                    <p class="text-xs leading-relaxed text-zinc-500 dark:text-zinc-400">{{ step.description }}</p>
                    <p class="mt-auto border-t border-zinc-200 pt-2 text-[11px] font-medium text-brand-700 dark:border-zinc-800 dark:text-brand-400">
                        {{ step.note }}
                    </p>
                </article>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            <div
                v-for="decision in decisions"
                :key="decision.title"
                class="rounded-lg border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900"
            >
                <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">{{ decision.title }}</h3>
                <ul class="mt-3 space-y-2 text-sm text-zinc-600 dark:text-zinc-300">
                    <li v-for="item in decision.items" :key="item" class="flex gap-2">
                        <i class="bi bi-dot text-lg leading-none text-brand-600 dark:text-brand-400" aria-hidden="true" />
                        <span>{{ item }}</span>
                    </li>
                </ul>
            </div>
        </section>

        <p class="text-center text-xs text-zinc-400 dark:text-zinc-500">
            Fluxograma de referência para a intranet corporativa. Textos e etapas podem ser ajustados conforme atualizações do processo.
        </p>
    </div>
</template>
