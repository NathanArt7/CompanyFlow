<script setup lang="ts">
import { Search } from 'lucide-vue-next'
import { ref, watch } from 'vue'

const emit = defineEmits<{
  'update:filters': [{ search: string, from_date: string, to_date: string }]
}>()

const search = ref('')
const fromDate = ref('')
const toDate = ref('')

let debounceTimer: ReturnType<typeof setTimeout> | undefined

function emitFilters() {
  emit('update:filters', { search: search.value, from_date: fromDate.value, to_date: toDate.value })
}

watch(search, () => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(emitFilters, 300)
})

watch([fromDate, toDate], emitFilters)
</script>

<template>
  <div class="bg-surface border border-border rounded-xl p-5 flex flex-col sm:flex-row sm:items-center gap-3">
    <div class="relative flex-1">
      <Search class="w-4 h-4 text-muted absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
      <input
        v-model="search"
        type="text"
        placeholder="Rechercher par nom et prénom..."
        class="w-full bg-background border border-border rounded-lg pl-10 pr-4 py-2.5 text-sm text-foreground placeholder:text-muted/60 focus:outline-none focus:border-primary transition-colors"
      >
    </div>

    <div class="flex items-center gap-2">
      <input
        v-model="fromDate"
        type="date"
        class="bg-background border border-border rounded-lg px-3 py-2.5 text-sm text-foreground focus:outline-none focus:border-primary transition-colors"
      >
      <span class="text-muted text-sm">à</span>
      <input
        v-model="toDate"
        type="date"
        class="bg-background border border-border rounded-lg px-3 py-2.5 text-sm text-foreground focus:outline-none focus:border-primary transition-colors"
      >
    </div>
  </div>
</template>
