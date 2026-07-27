<script setup lang="ts">
import type { AdminStatus } from '../types'

defineProps<{
  status: AdminStatus
}>()

const config: Record<AdminStatus, { label: string; dotClass: string; textClass: string }> = {
  active: { label: 'Actif', dotClass: 'bg-green-500', textClass: 'text-green-500' },
  pending: { label: 'En attente', dotClass: 'bg-blue-400', textClass: 'text-blue-400' },
  suspended: { label: 'Suspendu', dotClass: 'bg-red-500', textClass: 'text-red-400' },
}
</script>

<template>
  <span class="inline-flex items-center gap-1.5 text-sm font-medium" :class="config[status].textClass">
    <span class="w-1.5 h-1.5 rounded-full" :class="config[status].dotClass" />
    {{ config[status].label }}
  </span>
</template>