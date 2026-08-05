<script setup lang="ts">
import { Search } from 'lucide-vue-next'
import { ref, watch } from 'vue'

const emit = defineEmits<{
  'update:search': [string]
}>()

const searchQuery = ref('')
let debounceTimer: ReturnType<typeof setTimeout> | undefined

watch(searchQuery, () => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => emit('update:search', searchQuery.value), 300)
})
</script>

<template>
  <div class="relative">
    <Search class="w-4 h-4 text-muted absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
    <input
      v-model="searchQuery"
      type="text"
      placeholder="Rechercher une catégorie..."
      class="w-full bg-background border border-border rounded-lg pl-10 pr-4 py-2.5 text-sm text-foreground placeholder:text-muted/60 focus:outline-none focus:border-primary transition-colors"
    >
  </div>
</template>
