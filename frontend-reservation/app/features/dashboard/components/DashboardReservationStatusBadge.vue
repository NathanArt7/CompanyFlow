<script setup lang="ts">
import type { ReservationStatus } from '../type'

defineProps<{
  status: ReservationStatus
}>()

const statusConfig: Record<ReservationStatus, { label: string; class: string }> = {
  confirmed: { label: 'Confirmée', class: 'bg-green-500/10 text-green-500 border-green-500/20' },
  pending: { label: 'En attente', class: 'bg-orange-500/10 text-orange-500 border-orange-500/20' },
  cancelled: { label: 'Annulée', class: 'bg-red-500/10 text-red-400 border-red-500/20' },
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
