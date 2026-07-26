<script setup lang="ts">
import { Search, RotateCcw } from 'lucide-vue-next'
import { ref } from 'vue'

const searchQuery = ref('')
const selectedCategory = ref('all')
const selectedState = ref('all')
const selectedUsageType = ref('all')

const resetFilters = () => {
  searchQuery.value = ''
  selectedCategory.value = 'all'
  selectedState.value = 'all'
  selectedUsageType.value = 'all'
}
</script>

<template>
  <div class="flex flex-col lg:flex-row lg:items-center gap-3">
    <div class="relative flex-1 min-w-0">
      <Search class="w-4 h-4 text-muted absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
      <input
        v-model="searchQuery"
        type="text"
        placeholder="Rechercher un équipement..."
        class="w-full bg-background border border-border rounded-lg pl-10 pr-4 py-2.5 text-sm text-foreground placeholder:text-muted/60 focus:outline-none focus:border-primary transition-colors"
      >
    </div>

    <select v-model="selectedCategory" class="bg-background border border-border rounded-lg px-3 py-2.5 text-sm text-foreground focus:outline-none focus:border-primary transition-colors">
      <option value="all">Toutes les catégories</option>
      <option value="videoprojecteurs">Vidéoprojecteurs</option>
      <option value="ordinateurs">Ordinateurs</option>
      <option value="audio">Audio</option>
      <option value="accessoires">Accessoires</option>
      <option value="visioconference">Visioconférence</option>
    </select>

    <select v-model="selectedState" class="bg-background border border-border rounded-lg px-3 py-2.5 text-sm text-foreground focus:outline-none focus:border-primary transition-colors">
      <option value="all">Tous les états</option>
      <option value="available">Disponible</option>
      <option value="occupied">Occupé</option>
      <option value="maintenance">En maintenance</option>
      <option value="functional">Fonctionnel</option>
      <option value="broken">En panne</option>
      <option value="out_of_service">Hors service</option>
    </select>

    <select v-model="selectedUsageType" class="bg-background border border-border rounded-lg px-3 py-2.5 text-sm text-foreground focus:outline-none focus:border-primary transition-colors">
      <option value="all">Tous les types d'usage</option>
      <option value="borrowable">Empruntable</option>
      <option value="fixed">Non empruntable</option>
    </select>

    <button class="flex items-center gap-1.5 text-muted hover:text-foreground text-sm transition-colors shrink-0" @click="resetFilters">
      <RotateCcw class="w-3.5 h-3.5" />
      Réinitialiser
    </button>
  </div>
</template>