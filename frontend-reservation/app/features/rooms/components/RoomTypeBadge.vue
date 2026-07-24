<script setup lang="ts">
import type { RoomType } from '../types'

defineProps<{
  type: RoomType
}>()

const typeConfig: Record<RoomType, { label: string; class: string }> = {
  reunion: { label: 'RÉUNION', class: 'bg-primary/15 text-primary-light' },
  stockage: { label: 'STOCKAGE', class: 'bg-muted/15 text-muted' },
}
</script>

<template>
  <span
    class="inline-flex items-center text-xs font-semibold tracking-wide px-2.5 py-1 rounded-md"
    :class="typeConfig[type].class"
  >
    {{ typeConfig[type].label }}
  </span>
</template>