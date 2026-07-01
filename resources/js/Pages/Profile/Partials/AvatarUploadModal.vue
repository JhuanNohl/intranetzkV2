<script setup>
import { computed, ref, watch } from 'vue'

const open = defineModel('open', { type: Boolean, default: false })

const props = defineProps({
    processing: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['save'])

const FRAME = 260
const OUTPUT = 480

const fileInput = ref(null)
const cropImg = ref(null)
const imageSrc = ref('')
const error = ref('')

const naturalWidth = ref(0)
const naturalHeight = ref(0)
const baseScale = ref(1)
const zoom = ref(1)
const imgX = ref(0)
const imgY = ref(0)

const dragging = ref(false)
const dragStart = ref({ x: 0, y: 0, imgX: 0, imgY: 0 })

const displayScale = computed(() => baseScale.value * zoom.value)
const hasImage = computed(() => imageSrc.value !== '')

function reset() {
    imageSrc.value = ''
    error.value = ''
    naturalWidth.value = 0
    naturalHeight.value = 0
    zoom.value = 1
    imgX.value = 0
    imgY.value = 0
    if (fileInput.value) fileInput.value.value = ''
}

function closeModal() {
    open.value = false
    reset()
}

function openFilePicker() {
    fileInput.value?.click()
}

function onFileSelected(event) {
    const file = event.target.files[0] ?? null
    if (!file) return

    if (!['image/jpeg', 'image/png'].includes(file.type)) {
        error.value = 'Selecione uma imagem no formato JPG ou PNG.'
        return
    }

    error.value = ''

    const img = new Image()
    img.onload = () => {
        naturalWidth.value = img.naturalWidth
        naturalHeight.value = img.naturalHeight
        baseScale.value = Math.max(FRAME / img.naturalWidth, FRAME / img.naturalHeight)
        zoom.value = 1
        imgX.value = (FRAME - img.naturalWidth * baseScale.value) / 2
        imgY.value = (FRAME - img.naturalHeight * baseScale.value) / 2
        imageSrc.value = img.src
    }
    img.src = URL.createObjectURL(file)
}

function clampPosition() {
    const dispW = naturalWidth.value * displayScale.value
    const dispH = naturalHeight.value * displayScale.value

    imgX.value = Math.min(0, Math.max(FRAME - dispW, imgX.value))
    imgY.value = Math.min(0, Math.max(FRAME - dispH, imgY.value))
}

watch(zoom, (newZoom, oldZoom) => {
    const oldScale = baseScale.value * oldZoom
    const newScale = baseScale.value * newZoom

    const centerNaturalX = (FRAME / 2 - imgX.value) / oldScale
    const centerNaturalY = (FRAME / 2 - imgY.value) / oldScale

    imgX.value = FRAME / 2 - centerNaturalX * newScale
    imgY.value = FRAME / 2 - centerNaturalY * newScale

    clampPosition()
})

function startDrag(event) {
    if (!hasImage.value) return

    dragging.value = true
    const point = event.touches ? event.touches[0] : event
    dragStart.value = { x: point.clientX, y: point.clientY, imgX: imgX.value, imgY: imgY.value }
}

function onDrag(event) {
    if (!dragging.value) return

    const point = event.touches ? event.touches[0] : event
    imgX.value = dragStart.value.imgX + (point.clientX - dragStart.value.x)
    imgY.value = dragStart.value.imgY + (point.clientY - dragStart.value.y)
    clampPosition()
}

function stopDrag() {
    dragging.value = false
}

function save() {
    const canvas = document.createElement('canvas')
    canvas.width = OUTPUT
    canvas.height = OUTPUT

    const ctx = canvas.getContext('2d')
    const cropX = -imgX.value / displayScale.value
    const cropY = -imgY.value / displayScale.value
    const cropSize = FRAME / displayScale.value

    ctx.drawImage(cropImg.value, cropX, cropY, cropSize, cropSize, 0, 0, OUTPUT, OUTPUT)

    canvas.toBlob((blob) => {
        emit('save', new File([blob], 'avatar.jpg', { type: 'image/jpeg' }))
    }, 'image/jpeg', 0.92)
}
</script>

<template>
    <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="closeModal">
        <div class="w-full max-w-md rounded-xl border border-zinc-200 bg-white p-5 shadow-2xl dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Alterar foto de perfil</h2>
                <button type="button" class="rounded-lg p-1.5 text-zinc-400 transition hover:bg-zinc-100 dark:hover:bg-zinc-800" @click="closeModal">
                    <i class="bi bi-x-lg text-sm leading-none" aria-hidden="true" />
                </button>
            </div>

            <input ref="fileInput" type="file" accept=".jpg,.jpeg,.png,image/jpeg,image/png" class="hidden" @change="onFileSelected">

            <div v-if="!hasImage" class="mt-4">
                <button
                    type="button"
                    class="flex min-h-48 w-full flex-col items-center justify-center gap-2 rounded-lg border-2 border-dashed border-zinc-300 text-zinc-500 transition hover:border-brand-500 hover:text-brand-600 dark:border-zinc-700 dark:text-zinc-400"
                    @click="openFilePicker"
                >
                    <i class="bi bi-cloud-arrow-up text-3xl leading-none" aria-hidden="true" />
                    <span class="text-sm font-medium">Selecionar imagem JPG ou PNG</span>
                </button>
                <p v-if="error" class="mt-2 text-sm font-medium text-red-600 dark:text-red-400">{{ error }}</p>
            </div>

            <div v-else class="mt-4 space-y-4">
                <div
                    class="relative mx-auto overflow-hidden rounded-full border border-zinc-200 bg-zinc-100 dark:border-zinc-800 dark:bg-zinc-950"
                    :style="{ width: `${FRAME}px`, height: `${FRAME}px`, cursor: dragging ? 'grabbing' : 'grab' }"
                    @mousedown="startDrag"
                    @mousemove="onDrag"
                    @mouseup="stopDrag"
                    @mouseleave="stopDrag"
                    @touchstart="startDrag"
                    @touchmove="onDrag"
                    @touchend="stopDrag"
                >
                    <img
                        ref="cropImg"
                        :src="imageSrc"
                        alt=""
                        class="absolute select-none"
                        draggable="false"
                        :style="{
                            width: `${naturalWidth * displayScale}px`,
                            height: `${naturalHeight * displayScale}px`,
                            left: `${imgX}px`,
                            top: `${imgY}px`,
                        }"
                    >
                </div>

                <div class="flex items-center gap-3">
                    <i class="bi bi-dash-lg text-sm text-zinc-400" aria-hidden="true" />
                    <input v-model.number="zoom" type="range" min="1" max="3" step="0.02" class="flex-1 accent-brand-600">
                    <i class="bi bi-plus-lg text-sm text-zinc-400" aria-hidden="true" />
                </div>

                <div class="flex items-center justify-between">
                    <button type="button" class="text-sm font-medium text-zinc-500 transition hover:text-zinc-700 dark:hover:text-zinc-300" @click="reset">
                        Escolher outra imagem
                    </button>
                    <p class="text-xs text-zinc-400 dark:text-zinc-500">Arraste para ajustar o enquadramento</p>
                </div>
            </div>

            <div class="mt-5 flex justify-end gap-2">
                <button type="button" class="rounded-lg px-4 py-2 text-sm font-medium text-zinc-600 transition hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800" @click="closeModal">
                    Cancelar
                </button>
                <button
                    type="button"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-brand-700 disabled:cursor-not-allowed disabled:opacity-60"
                    :disabled="!hasImage || processing"
                    @click="save"
                >
                    {{ processing ? 'Salvando...' : 'Salvar foto' }}
                </button>
            </div>
        </div>
    </div>
</template>
