<script setup lang="ts">
import type { RoomStatus } from '../types'

defineProps<{
  status: RoomStatus
}>()

const statusConfig: Record<RoomStatus, { label: string; dotClass: string; textClass: string }> = {
  available: { label: 'Disponible', dotClass: 'bg-green-500', textClass: 'text-green-500' },
  occupied: { label: 'Occupée', dotClass: 'bg-orange-500', textClass: 'text-orange-500' },
  maintenance: { label: 'En maintenance', dotClass: 'bg-red-500', textClass: 'text-red-400' },
  out_of_service: { label: 'Hors service', dotClass: 'bg-muted', textClass: 'text-muted' },
}
</script>

<template>
  <span class="inline-flex items-center gap-1.5 text-sm font-medium" :class="statusConfig[status].textClass">
    <span class="w-1.5 h-1.5 rounded-full" :class="statusConfig[status].dotClass" />
    {{ statusConfig[status].label }}
  </span>
</template>