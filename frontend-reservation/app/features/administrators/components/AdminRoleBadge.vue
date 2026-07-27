<script setup lang="ts">
import type { AdminRole } from '../types'

defineProps<{
  role: AdminRole
}>()

const config: Record<AdminRole, { label: string; class: string }> = {
  super_admin: { label: 'Super Administrateur', class: 'text-primary-light' },
  admin: { label: 'Administrateur', class: 'text-blue-400' },
}
</script>

<template>
  <span class="text-sm font-medium" :class="config[role].class">
    {{ config[role].label }}
  </span>
</template>