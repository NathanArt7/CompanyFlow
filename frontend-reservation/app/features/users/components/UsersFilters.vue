<script setup lang="ts">
import { Search, RotateCcw } from 'lucide-vue-next'
import { ref } from 'vue'

const searchQuery = ref('')
const selectedRole = ref('all')
const selectedStatus = ref('all')

const resetFilters = () => {
  searchQuery.value = ''
  selectedRole.value = 'all'
  selectedStatus.value = 'all'
}
</script>

<template>
  <div class="flex flex-col lg:flex-row lg:items-center gap-3">
    <div class="relative flex-1 min-w-0">
      <Search class="w-4 h-4 text-muted absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
      <input
        v-model="searchQuery"
        type="text"
        placeholder="Rechercher un utilisateur..."
        class="w-full bg-background border border-border rounded-lg pl-10 pr-4 py-2.5 text-sm text-foreground placeholder:text-muted/60 focus:outline-none focus:border-primary transition-colors"
      >
    </div>

    <select v-model="selectedRole" class="bg-background border border-border rounded-lg px-3 py-2.5 text-sm text-foreground focus:outline-none focus:border-primary transition-colors">
      <option value="all">Tous les rôles</option>
      <option value="super_admin">Super Administrateur</option>
      <option value="admin">Administrateur</option>
      <option value="super_employee">Super Employé</option>
      <option value="employee">Employé</option>
      <option value="technician">Technicien</option>
    </select>

    <select v-model="selectedStatus" class="bg-background border border-border rounded-lg px-3 py-2.5 text-sm text-foreground focus:outline-none focus:border-primary transition-colors">
      <option value="all">Tous les statuts</option>
      <option value="active">Actif</option>
      <option value="pending">En attente</option>
      <option value="disabled">Désactivé</option>
      <option value="suspended">Suspendu</option>
    </select>

    <button class="flex items-center gap-1.5 text-muted hover:text-foreground text-sm transition-colors shrink-0" @click="resetFilters">
      <RotateCcw class="w-3.5 h-3.5" />
      Réinitialiser
    </button>
  </div>
</template>