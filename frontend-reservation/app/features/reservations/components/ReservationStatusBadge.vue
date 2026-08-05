<script setup lang="ts">
import type { ReservationStatus } from '../type'

defineProps<{
  status: ReservationStatus
}>()

const statusConfig: Record<ReservationStatus, { label: string; class: string }> = {
  confirmed: { label: 'Confirmée', class: 'bg-green-500/10 text-green-500 border-green-500/20' },
  ongoing: { label: 'En cours', class: 'bg-blue-500/10 text-blue-400 border-blue-500/20' },
  cancelled: { label: 'Annulée', class: 'bg-red-500/10 text-red-400 border-red-500/20' },
  past: { label: 'Passée', class: 'bg-gray-500/10 text-muted border-gray-500/20' },
}
</script>

<template>
  <span
    class="inline-flex items-center text-xs font-medium px-2.5 py-1 rounded-full border"
    :class="statusConfig[status].class"
  >
    {{ statusConfig[status].label }}
  </span>
</template>