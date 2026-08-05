<script setup lang="ts">
import { Search } from 'lucide-vue-next'

defineProps<{
  modelValue: string
}>()

defineEmits<{
  'update:modelValue': [string]
}>()
</script>

<template>
  <div class="relative">
    <Search class="w-4 h-4 text-muted absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
    <input
      :value="modelValue"
      type="text"
      placeholder="Rechercher une salle ou un équipement..."
      class="w-full bg-background border border-border rounded-lg pl-10 pr-4 py-2.5 text-sm text-foreground placeholder:text-muted/60 focus:outline-none focus:border-primary transition-colors"
      @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
    >
  </div>
</template>
