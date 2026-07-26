<script setup lang="ts">
import { Monitor, CheckCircle2, MonitorCheck, Wrench, XCircle } from 'lucide-vue-next'
import type { EquipmentStat } from '../types'

const stats: EquipmentStat[] = [
  { icon: Monitor, iconBg: 'bg-primary/20 text-primary-light', value: 48, label: 'Total équipements', subtext: 'Toutes catégories confondues', barColor: '', percentage: 0 },
  { icon: CheckCircle2, iconBg: 'bg-green-500/20 text-green-500', value: 26, label: 'Disponibles', subtext: '54% du total', barColor: 'bg-green-500', percentage: 54 },
  { icon: MonitorCheck, iconBg: 'bg-orange-500/20 text-orange-500', value: 12, label: 'Occupés', subtext: '25% du total', barColor: 'bg-orange-500', percentage: 25 },
  { icon: Wrench, iconBg: 'bg-blue-500/20 text-blue-400', value: 6, label: 'En maintenance', subtext: '13% du total', barColor: 'bg-blue-500', percentage: 13 },
  { icon: XCircle, iconBg: 'bg-red-500/20 text-red-400', value: 4, label: 'Hors service', subtext: '8% du total', barColor: 'bg-red-500', percentage: 8 },
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