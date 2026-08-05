<script setup lang="ts">
import type { UserStatus } from '../types'

defineProps<{
  status: UserStatus
}>()

const config: Record<UserStatus, { label: string; dotClass: string; textClass: string }> = {
  active: { label: 'Actif', dotClass: 'bg-green-500', textClass: 'text-green-500' },
  disabled: { label: 'Désactivé', dotClass: 'bg-orange-500', textClass: 'text-orange-500' },
}
</script>

<template>
  <span class="inline-flex items-center gap-1.5 text-sm font-medium" :class="config[status].textClass">
    <span class="w-1.5 h-1.5 rounded-full" :class="config[status].dotClass" />
    {{ config[status].label }}
  </span>
</template>