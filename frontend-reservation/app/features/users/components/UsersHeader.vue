<script setup lang="ts">
import { Plus } from 'lucide-vue-next'
import type { ExportColumn, ExportRow } from '~/composables/useTableExport'

defineProps<{
  exportColumns: ExportColumn[]
  exportRows: ExportRow[]
}>()

const emit = defineEmits<{
  'create-click': []
}>()
</script>

<template>
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <h1 class="text-foreground text-2xl md:text-3xl font-bold">Utilisateurs</h1>
      <p class="text-muted text-sm mt-1">Gérez tous les utilisateurs de votre entreprise.</p>
    </div>

    <div class="flex items-center gap-3 shrink-0">
      <button
        class="flex items-center gap-2 bg-primary hover:bg-primary-hover text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors"
        @click="emit('create-click')"
      >
        <Plus class="w-4 h-4" />
        Ajouter un utilisateur
      </button>

      <ExportMenu
        filename="utilisateurs"
        title="Utilisateurs"
        :columns="exportColumns"
        :rows="exportRows"
      />
    </div>
  </div>
</template>