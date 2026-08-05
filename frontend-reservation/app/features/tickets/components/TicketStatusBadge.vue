<script setup lang="ts">
import type { TicketStatus } from '../type'

defineProps<{
  status: TicketStatus
}>()

const statusConfig: Record<TicketStatus, { label: string, class: string }> = {
  OUVERT: { label: 'Ouvert', class: 'bg-red-500/10 text-red-400 border-red-500/20' },
  EN_COURS: { label: 'En cours', class: 'bg-blue-500/10 text-blue-400 border-blue-500/20' },
  RESOLU: { label: 'Résolu', class: 'bg-green-500/10 text-green-500 border-green-500/20' },
  FERME: { label: 'Fermé', class: 'bg-gray-500/10 text-muted border-gray-500/20' },
}
</script>

<template>
  <span
    class="inline-flex items-center whitespace-nowrap text-xs font-medium px-2.5 py-1 rounded-full border"
    :class="statusConfig[status].class"
  >
    {{ statusConfig[status].label }}
  </span>
</template>
