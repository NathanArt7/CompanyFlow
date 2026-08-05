<script setup lang="ts">
import { Search, RotateCcw } from 'lucide-vue-next'
import { ref, watch, onMounted } from 'vue'
import type { EquipmentFilters } from '../type'
import type { RawEquipmentCategory } from '~/features/equipment-categories/type'
import { useEquipmentCategoryService } from '~/features/equipment-categories/services/equipment-category.service'

const emit = defineEmits<{
  'update:filters': [EquipmentFilters]
}>()

const equipmentCategoryService = useEquipmentCategoryService()

const categories = ref<RawEquipmentCategory[]>([])
const categoriesAvailable = ref(true)

const searchQuery = ref('')
const selectedCategory = ref('all')
const selectedState = ref('all')
const selectedUsageType = ref('all')

let debounceTimer: ReturnType<typeof setTimeout> | undefined

function emitFilters() {
  emit('update:filters', {
    search: searchQuery.value,
    categoryId: selectedCategory.value === 'all' ? null : Number(selectedCategory.value),
    usageType: selectedUsageType.value === 'all' ? null : selectedUsageType.value as 'EMPRUNTABLE' | 'NON_EMPRUNTABLE',
    etat: selectedState.value === 'all' ? null : selectedState.value,
  })
}

watch(searchQuery, () => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(emitFilters, 300)
})

const resetFilters = () => {
  searchQuery.value = ''
  selectedCategory.value = 'all'
  selectedState.value = 'all'
  selectedUsageType.value = 'all'
  emitFilters()
}

onMounted(async () => {
  try {
    categories.value = await equipmentCategoryService.list()
  } catch {
    categoriesAvailable.value = false
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
        placeholder="Rechercher un équipement..."
        class="w-full bg-background border border-border rounded-lg pl-10 pr-4 py-2.5 text-sm text-foreground placeholder:text-muted/60 focus:outline-none focus:border-primary transition-colors"
      >
    </div>

    <select
      v-model="selectedCategory"
      :disabled="!categoriesAvailable"
      class="bg-background border border-border rounded-lg px-3 py-2.5 text-sm text-foreground focus:outline-none focus:border-primary transition-colors disabled:opacity-60"
      @change="emitFilters"
    >
      <option value="all">Toutes les catégories</option>
      <option v-for="category in categories" :key="category.id" :value="String(category.id)">
        {{ category.nom }}
      </option>
    </select>

    <select
      v-model="selectedState"
      class="bg-background border border-border rounded-lg px-3 py-2.5 text-sm text-foreground focus:outline-none focus:border-primary transition-colors"
      @change="emitFilters"
    >
      <option value="all">Tous les états</option>
      <option value="DISPONIBLE">Disponible</option>
      <option value="OCCUPE">Occupé</option>
      <option value="FONCTIONNEL">Fonctionnel</option>
      <option value="EN_PANNE">En panne</option>
      <option value="EN_MAINTENANCE">En maintenance</option>
      <option value="HORS_SERVICE">Hors service</option>
    </select>

    <select
      v-model="selectedUsageType"
      class="bg-background border border-border rounded-lg px-3 py-2.5 text-sm text-foreground focus:outline-none focus:border-primary transition-colors"
      @change="emitFilters"
    >
      <option value="all">Tous les types d'usage</option>
      <option value="EMPRUNTABLE">Empruntable</option>
      <option value="NON_EMPRUNTABLE">Non empruntable</option>
    </select>

    <button class="flex items-center gap-1.5 text-muted hover:text-foreground text-sm transition-colors shrink-0" @click="resetFilters">
      <RotateCcw class="w-3.5 h-3.5" />
      Réinitialiser
    </button>
  </div>
</template>
