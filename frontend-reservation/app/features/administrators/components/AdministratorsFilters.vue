<script setup lang="ts">
import { Search, Calendar, RotateCcw } from 'lucide-vue-next'
import { ref } from 'vue'

const searchQuery = ref('')
const selectedStatus = ref('all')
const selectedRole = ref('all')
const selectedDate = ref('all')

const resetFilters = () => {
  searchQuery.value = ''
  selectedStatus.value = 'all'
  selectedRole.value = 'all'
  selectedDate.value = 'all'
}
</script>

<template>
  <div class="flex flex-col lg:flex-row lg:items-center gap-3">
    <div class="relative flex-1 min-w-0">
      <Search class="w-4 h-4 text-muted absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
      <input
        v-model="searchQuery"
        type="text"
        placeholder="Rechercher un administrateur..."
        class="w-full bg-background border border-border rounded-lg pl-10 pr-4 py-2.5 text-sm text-foreground placeholder:text-muted/60 focus:outline-none focus:border-primary transition-colors"
      >
    </div>

    <select v-model="selectedStatus" class="bg-background border border-border rounded-lg px-3 py-2.5 text-sm text-foreground focus:outline-none focus:border-primary transition-colors">
      <option value="all">Tous les statuts</option>
      <option value="active">Actif</option>
      <option value="pending">En attente</option>
      <option value="suspended">Suspendu</option>
    </select>

    <select v-model="selectedRole" class="bg-background border border-border rounded-lg px-3 py-2.5 text-sm text-foreground focus:outline-none focus:border-primary transition-colors">
      <option value="all">Tous les rôles admin</option>
      <option value="super_admin">Super Administrateur</option>
      <option value="admin">Administrateur</option>
    </select>

    <div class="relative">
      <Calendar class="w-4 h-4 text-muted absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" />
      <select v-model="selectedDate" class="bg-background border border-border rounded-lg pl-9 pr-3 py-2.5 text-sm text-foreground focus:outline-none focus:border-primary transition-colors appearance-none">
        <option value="all">Créé le</option>
        <option value="7d">7 derniers jours</option>
        <option value="30d">30 derniers jours</option>
        <option value="year">Cette année</option>
      </select>
    </div>

    <button class="flex items-center gap-1.5 text-muted hover:text-foreground text-sm transition-colors shrink-0" @click="resetFilters">
      <RotateCcw class="w-3.5 h-3.5" />
      Réinitialiser
    </button>
  </div>
</template>