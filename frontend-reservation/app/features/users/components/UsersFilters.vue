<script setup lang="ts">
import { Search, RotateCcw } from 'lucide-vue-next'
import { ref, watch, onMounted } from 'vue'
import type { RawRole, UserFilters } from '../type'
import { useUserService } from '../services/user.service'

const emit = defineEmits<{
  'update:filters': [UserFilters]
}>()

const userService = useUserService()

const roles = ref<RawRole[]>([])
const rolesAvailable = ref(true)

const searchQuery = ref('')
const selectedRole = ref('all')
const selectedStatus = ref('all')

let debounceTimer: ReturnType<typeof setTimeout> | undefined

function emitFilters() {
  emit('update:filters', {
    search: searchQuery.value,
    roleId: selectedRole.value === 'all' ? null : Number(selectedRole.value),
    actif: selectedStatus.value === 'all' ? null : selectedStatus.value === 'active',
  })
}

watch(searchQuery, () => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(emitFilters, 300)
})

const resetFilters = () => {
  searchQuery.value = ''
  selectedRole.value = 'all'
  selectedStatus.value = 'all'
  emitFilters()
}

onMounted(async () => {
  try {
    roles.value = await userService.listRoles()
  } catch {
    rolesAvailable.value = false
  }
})
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

    <select
      v-model="selectedRole"
      :disabled="!rolesAvailable"
      class="bg-background border border-border rounded-lg px-3 py-2.5 text-sm text-foreground focus:outline-none focus:border-primary transition-colors disabled:opacity-60"
      @change="emitFilters"
    >
      <option value="all">Tous les rôles</option>
      <option v-for="role in roles" :key="role.id" :value="String(role.id)">
        {{ role.nom.replace('_', ' ') }}
      </option>
    </select>

    <select
      v-model="selectedStatus"
      class="bg-background border border-border rounded-lg px-3 py-2.5 text-sm text-foreground focus:outline-none focus:border-primary transition-colors"
      @change="emitFilters"
    >
      <option value="all">Tous les statuts</option>
      <option value="active">Actif</option>
      <option value="disabled">Désactivé</option>
    </select>

    <button class="flex items-center gap-1.5 text-muted hover:text-foreground text-sm transition-colors shrink-0" @click="resetFilters">
      <RotateCcw class="w-3.5 h-3.5" />
      Réinitialiser
    </button>
  </div>
</template>
