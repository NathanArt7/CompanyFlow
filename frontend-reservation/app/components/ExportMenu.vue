<script setup lang="ts">
import { Upload, FileText, FileSpreadsheet } from 'lucide-vue-next'
import { ref, onMounted, onUnmounted } from 'vue'
import type { ExportColumn, ExportRow } from '~/composables/useTableExport'

const props = defineProps<{
  filename: string
  title: string
  columns: ExportColumn[]
  rows: ExportRow[]
}>()

const { exportToPdf, exportToExcel } = useTableExport()

const isOpen = ref(false)
const isExporting = ref(false)

function toggle() {
  isOpen.value = !isOpen.value
}

function close() {
  isOpen.value = false
}

async function handlePdf() {
  isExporting.value = true
  try {
    await exportToPdf(props.filename, props.title, props.columns, props.rows)
  } finally {
    isExporting.value = false
    close()
  }
}

async function handleExcel() {
  isExporting.value = true
  try {
    await exportToExcel(props.filename, props.title, props.columns, props.rows)
  } finally {
    isExporting.value = false
    close()
  }
}

onMounted(() => document.addEventListener('click', close))
onUnmounted(() => document.removeEventListener('click', close))
</script>

<template>
  <div class="relative" @click.stop>
    <button
      :disabled="isExporting"
      class="flex items-center gap-2 bg-surface hover:bg-border border border-border text-foreground text-sm font-medium px-4 py-2.5 rounded-lg transition-colors disabled:opacity-60"
      @click="toggle"
    >
      <Upload class="w-4 h-4" />
      Exporter
    </button>

    <div
      v-if="isOpen"
      class="absolute right-0 top-full mt-2 w-44 bg-surface border border-border rounded-lg shadow-lg overflow-hidden z-10"
    >
      <button
        class="w-full flex items-center gap-2 px-3 py-2.5 text-sm text-foreground hover:bg-border/50 transition-colors"
        @click="handlePdf"
      >
        <FileText class="w-3.5 h-3.5" />
        Exporter en PDF
      </button>
      <button
        class="w-full flex items-center gap-2 px-3 py-2.5 text-sm text-foreground hover:bg-border/50 transition-colors"
        @click="handleExcel"
      >
        <FileSpreadsheet class="w-3.5 h-3.5" />
        Exporter en Excel
      </button>
    </div>
  </div>
</template>
