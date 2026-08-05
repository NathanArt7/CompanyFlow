<script setup lang="ts">
import { LayoutGrid, Plus } from 'lucide-vue-next'
import { computed } from 'vue'
import type { ExportColumn, ExportRow } from '~/composables/useTableExport'

defineProps<{
  exportColumns: ExportColumn[]
  exportRows: ExportRow[]
}>()

const emit = defineEmits<{
  'create-click': []
}>()

const authStore = useAuthStore()

// Le Technicien accède à la page (pour changer l'état du matériel depuis un ticket)
// mais ne gère pas le parc : pas de création, pas de catégories, pas d'export.
const canManageEquipments = computed(() => authStore.permissions.includes('creer_materiel'))
</script>

<template>
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <h1 class="text-foreground text-2xl md:text-3xl font-bold">Équipements</h1>
      <p class="text-muted text-sm mt-1">Gérez tous les équipements de votre entreprise.</p>
    </div>

    <div v-if="canManageEquipments" class="flex items-center gap-3 shrink-0">
      <button
        class="flex items-center gap-2 bg-primary hover:bg-primary-hover text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors"
        @click="emit('create-click')"
      >
        <Plus class="w-4 h-4" />
        Ajouter un équipement
      </button>

      <NuxtLink
        to="/equipment-categories"
        class="flex items-center gap-2 bg-surface hover:bg-border border border-border text-foreground text-sm font-medium px-4 py-2.5 rounded-lg transition-colors"
      >
        <LayoutGrid class="w-4 h-4" />
        Catégories d'équipements
      </NuxtLink>

      <ExportMenu
        filename="equipements"
        title="Équipements"
        :columns="exportColumns"
        :rows="exportRows"
      />
    </div>
  </div>
</template>
