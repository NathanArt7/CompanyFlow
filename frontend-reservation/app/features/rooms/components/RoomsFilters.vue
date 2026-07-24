<script setup lang="ts">
import { Search, RotateCcw } from 'lucide-vue-next'
import { ref } from 'vue'

const searchQuery = ref('')
const selectedType = ref('all')
const selectedStatus = ref('all')
const selectedCapacity = ref('all')

const resetFilters = () => {
  searchQuery.value = ''
  selectedType.value = 'all'
  selectedStatus.value = 'all'
  selectedCapacity.value = 'all'
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
    >
      <option value="all">Tous les types</option>
      <option value="reunion">Réunion</option>
      <option value="stockage">Stockage</option>
    </select>

    <select
      v-model="selectedStatus"
      class="bg-background border border-border rounded-lg px-3 py-2.5 text-sm text-foreground focus:outline-none focus:border-primary transition-colors"
    >
      <option value="all">Tous les statuts</option>
      <option value="available">Disponible</option>
      <option value="occupied">Occupée</option>
      <option value="maintenance">En maintenance</option>
    </select>

    <select
      v-model="selectedCapacity"
      class="bg-background border border-border rounded-lg px-3 py-2.5 text-sm text-foreground focus:outline-none focus:border-primary transition-colors"
    >
      <option value="all">Toutes les capacités</option>
      <option value="small">1-8 places</option>
      <option value="medium">9-16 places</option>
      <option value="large">17+ places</option>
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