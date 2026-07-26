<script setup lang="ts">
import type { EquipmentState } from '../types'

defineProps<{
  state: EquipmentState
}>()

const config: Record<EquipmentState, { label: string; dotClass: string; textClass: string }> = {
  available: { label: 'Disponible', dotClass: 'bg-green-500', textClass: 'text-green-500' },
  occupied: { label: 'Occupé', dotClass: 'bg-orange-500', textClass: 'text-orange-500' },
  maintenance: { label: 'En maintenance', dotClass: 'bg-blue-400', textClass: 'text-blue-400' },
  functional: { label: 'Fonctionnel', dotClass: 'bg-green-500', textClass: 'text-green-500' },
  broken: { label: 'En panne', dotClass: 'bg-red-500', textClass: 'text-red-400' },
  out_of_service: { label: 'Hors service', dotClass: 'bg-red-500', textClass: 'text-red-400' },
}
</script>

<template>
  <span class="inline-flex items-center gap-1.5 text-sm font-medium" :class="config[state].textClass">
    <span class="w-1.5 h-1.5 rounded-full" :class="config[state].dotClass" />
    {{ config[state].label }}
  </span>
</template>