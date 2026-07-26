<script setup lang="ts">
import type { EquipmentUsageType } from '../types'

defineProps<{
  type: EquipmentUsageType
}>()

const config: Record<EquipmentUsageType, { label: string; class: string }> = {
  borrowable: { label: 'Empruntable', class: 'bg-primary/15 text-primary-light' },
  fixed: { label: 'Non empruntable', class: 'bg-blue-500/15 text-blue-400' },
}
</script>

<template>
  <span class="inline-flex items-center text-xs font-medium px-2.5 py-1 rounded-md" :class="config[type].class">
    {{ config[type].label }}
  </span>
</template>