<script setup lang="ts">
import { Search, RotateCcw } from 'lucide-vue-next'
import { ref, watch } from 'vue'
import type { RoomFilters } from '../type'

const emit = defineEmits<{
  'update:filters': [RoomFilters]
}>()

const searchQuery = ref('')
const selectedType = ref('all')
const selectedStatus = ref('all')

let debounceTimer: ReturnType<typeof setTimeout> | undefined

function emitFilters() {
  emit('update:filters', {
    search: searchQuery.value,
    type: selectedType.value === 'all' ? null : selectedType.value as 'MEETING' | 'STORAGE',
    statut: selectedStatus.value === 'all' ? null : selectedStatus.value as RoomFilters['statut'],
  })
}

watch(searchQuery, () => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(emitFilters, 300)
})

const resetFilters = () => {
  searchQuery.value = ''
  selectedType.value = 'all'
  selectedStatus.value = 'all'
  emitFilters()
}
</script>

<template>
  <div class="flex flex-col lg:flex-row lg:items-center gap-3">
    <div class="relative flex-1 min-w-0">
      <Search class="w-4 h-4 text-muted absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
      <input
        v-model="searchQuery"
        type="text"
        placeholder="Rechercher une salle..."
        class="w-full bg-background border border-border rounded-lg pl-10 pr-4 py-2.5 text-sm text-foreground placeholder:text-muted/60 focus:outline-none focus:border-primary transition-colors"
      >
    </div>

    <select
      v-model="selectedType"
      class="bg-background border border-border rounded-lg px-3 py-2.5 text-sm text-foreground focus:outline-none focus:border-primary transition-colors"
      @change="emitFilters"
    >
      <option value="all">Tous les types</option>
      <option value="MEETING">Réunion</option>
      <option value="STORAGE">Stockage</option>
    </select>

    <select
      v-model="selectedStatus"
      class="bg-background border border-border rounded-lg px-3 py-2.5 text-sm text-foreground focus:outline-none focus:border-primary transition-colors"
      @change="emitFilters"
    >
      <option value="all">Tous les statuts</option>
      <option value="Disponible">Disponible</option>
      <option value="Occupée">Occupée</option>
      <option value="En maintenance">En maintenance</option>
      <option value="Hors service">Hors service</option>
    </select>

    <button
      class="flex items-center gap-1.5 text-muted hover:text-foreground text-sm transition-colors shrink-0"
      @click="resetFilters"
    >
      <RotateCcw class="w-3.5 h-3.5" />
      Réinitialiser
    </button>
  </div>
</template>
