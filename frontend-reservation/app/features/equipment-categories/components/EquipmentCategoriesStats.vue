<script setup lang="ts">
import { LayoutGrid } from 'lucide-vue-next'
import { onMounted, ref } from 'vue'
import { useEquipmentCategoryService } from '../services/equipment-category.service'

const equipmentCategoryService = useEquipmentCategoryService()

const total = ref(0)
const isLoading = ref(true)

async function load() {
  isLoading.value = true
  try {
    const response = await equipmentCategoryService.paginate({ per_page: 1 })
    total.value = response.total
  } finally {
    isLoading.value = false
  }
}

defineExpose({ refresh: load })

onMounted(load)
</script>

<template>
  <div v-if="isLoading" class="w-full sm:w-64">
    <SkeletonStatCard />
  </div>
  <div v-else class="bg-surface border border-border rounded-xl p-5 w-full sm:w-64">
    <div class="w-10 h-10 rounded-lg bg-primary flex items-center justify-center mb-4">
      <LayoutGrid class="w-5 h-5 text-white" />
    </div>
    <p class="text-foreground text-2xl font-bold">{{ total }}</p>
    <p class="text-muted text-sm">Catégories</p>
  </div>
</template>
