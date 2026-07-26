<script setup lang="ts">
import type { UserRole } from '../types'

defineProps<{
  role: UserRole
}>()

const config: Record<UserRole, { label: string; class: string }> = {
  super_admin: { label: 'Super Administrateur', class: 'text-primary-light' },
  admin: { label: 'Administrateur', class: 'text-blue-400' },
  super_employee: { label: 'Super Employé', class: 'text-cyan-400' },
  employee: { label: 'Employé', class: 'text-green-500' },
  technician: { label: 'Technicien', class: 'text-orange-500' },
}
</script>

<template>
  <span class="text-sm font-medium" :class="config[role].class">
    {{ config[role].label }}
  </span>
</template>