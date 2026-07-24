<script setup lang="ts">
import { RotateCcw } from 'lucide-vue-next'
import { ref } from 'vue'

const selectedRoom = ref('all')
const selectedStatus = ref('all')
const selectedOrganizer = ref('all')

const resetFilters = () => {
  selectedRoom.value = 'all'
  selectedStatus.value = 'all'
  selectedOrganizer.value = 'all'
}
</script>

<template>
  <div class="flex flex-col sm:flex-row sm:items-center gap-3 flex-wrap">
    <select
      v-model="selectedRoom"
      class="bg-background border border-border rounded-lg px-3 py-2 text-sm text-foreground focus:outline-none focus:border-primary transition-colors"
    >
      <option value="all">Toutes les salles</option>
      <option value="a">Salle de réunion A</option>
      <option value="b">Salle de réunion B</option>
      <option value="c">Salle de réunion C</option>
      <option value="conf">Salle de conférence</option>
    </select>

    <select
      v-model="selectedStatus"
      class="bg-background border border-border rounded-lg px-3 py-2 text-sm text-foreground focus:outline-none focus:border-primary transition-colors"
    >
      <option value="all">Tous les statuts</option>
      <option value="confirmed">Confirmée</option>
      <option value="pending">En attente</option>
      <option value="cancelled">Annulée</option>
    </select>

    <select
      v-model="selectedOrganizer"
      class="bg-background border border-border rounded-lg px-3 py-2 text-sm text-foreground focus:outline-none focus:border-primary transition-colors"
    >
      <option value="all">Tous les organisateurs</option>
      <option value="ml">Marc Lorem</option>
      <option value="sj">Sarah Johnson</option>
      <option value="dt">David Tchalla</option>
    </select>

    <button
      class="flex items-center gap-1.5 text-muted hover:text-foreground text-sm transition-colors"
      @click="resetFilters"
    >
      <RotateCcw class="w-3.5 h-3.5" />
      Réinitialiser
    </button>
  </div>
</template>