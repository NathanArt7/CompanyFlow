<script setup lang="ts">
import { Plus } from 'lucide-vue-next'
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

// Le Super Employé n'exporte pas de données et n'a pas besoin du descriptif de la page.
const isSuperEmploye = computed(() => authStore.user?.role === 'Super_Employe')

// Seuls les détenteurs de "reserver_salle" (Super Employé, Super Admin) peuvent réellement
// réserver une salle : l'Admin gère/valide/annule les réservations mais n'en crée pas pour
// lui-même. Sans ce garde-fou le bouton était visible pour tous et menait à une erreur 403
// une fois le formulaire rempli.
const canCreate = computed(() => authStore.permissions.includes('reserver_salle'))
</script>

<template>
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <h1 class="text-foreground text-2xl md:text-3xl font-bold">Réservations</h1>
      <p v-if="!isSuperEmploye" class="text-muted text-sm mt-1">
        Consultez et gérez toutes les réservations de votre entreprise.
      </p>
    </div>

    <div class="flex items-center gap-3 shrink-0">
      <button
        v-if="canCreate"
        class="flex items-center gap-2 bg-primary hover:bg-primary-hover text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors"
        @click="emit('create-click')"
      >
        <Plus class="w-4 h-4" />
        Nouvelle réservation
      </button>

      <ExportMenu
        v-if="!isSuperEmploye"
        filename="reservations"
        title="Réservations"
        :columns="exportColumns"
        :rows="exportRows"
      />
    </div>
  </div>
</template>