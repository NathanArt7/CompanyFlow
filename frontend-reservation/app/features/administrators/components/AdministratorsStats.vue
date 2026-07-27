<script setup lang="ts">
import { Users, UserCheck, UserCog, UserX } from 'lucide-vue-next'
import type { AdminStat } from '../types'

const stats: AdminStat[] = [
  { icon: Users, iconBg: 'bg-primary/20 text-primary-light', value: 5, label: 'Administrateurs', subtext: 'Total des administrateurs', barColor: '', percentage: 0 },
  { icon: UserCheck, iconBg: 'bg-green-500/20 text-green-500', value: 4, label: 'Actifs', subtext: '80% du total', barColor: 'bg-green-500', percentage: 80 },
  { icon: UserCog, iconBg: 'bg-orange-500/20 text-orange-500', value: 1, label: "En attente d'activation", subtext: '20% du total', barColor: 'bg-orange-500', percentage: 20 },
  { icon: UserX, iconBg: 'bg-red-500/20 text-red-400', value: 0, label: 'Suspendus', subtext: '0% du total', barColor: 'bg-red-500', percentage: 0 },
]
</script>

<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div v-for="stat in stats" :key="stat.label" class="bg-surface border border-border rounded-xl p-5">
      <div class="w-11 h-11 rounded-xl flex items-center justify-center mb-3" :class="stat.iconBg">
        <component :is="stat.icon" class="w-5 h-5" />
      </div>
      <p class="text-foreground text-2xl font-bold">{{ stat.value }}</p>
      <p class="text-foreground text-sm">{{ stat.label }}</p>
      <p class="text-muted text-xs mt-1">{{ stat.subtext }}</p>

      <div v-if="stat.barColor" class="w-full h-1 bg-background rounded-full overflow-hidden mt-3">
        <div class="h-full rounded-full" :class="stat.barColor" :style="{ width: `${stat.percentage}%` }" />
      </div>
    </div>
  </div>
</template>