<script setup lang="ts">
import { Users, UserCheck, UserCog, UserX, UserMinus } from 'lucide-vue-next'
import type { UserStat } from '../types'

const stats: UserStat[] = [
  { icon: Users, iconBg: 'bg-primary/20 text-primary-light', value: 86, label: 'Total utilisateurs', subtext: 'Tous rôles confondus', barColor: '', percentage: 0 },
  { icon: UserCheck, iconBg: 'bg-green-500/20 text-green-500', value: 64, label: 'Actifs', subtext: '74% du total', barColor: 'bg-green-500', percentage: 74 },
  { icon: UserCog, iconBg: 'bg-blue-500/20 text-blue-400', value: 5, label: "En attente d'activation", subtext: '6% du total', barColor: 'bg-blue-500', percentage: 6 },
  { icon: UserMinus, iconBg: 'bg-orange-500/20 text-orange-500', value: 13, label: 'Désactivés', subtext: '15% du total', barColor: 'bg-orange-500', percentage: 15 },
  { icon: UserX, iconBg: 'bg-red-500/20 text-red-400', value: 4, label: 'Suspendus', subtext: '5% du total', barColor: 'bg-red-500', percentage: 5 },
]
</script>

<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
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