<script setup lang="ts">
import { DoorOpen, Users, Monitor, MonitorX, Calendar } from 'lucide-vue-next'
import type { AvailabilityStat } from '../types'

const stats: AvailabilityStat[] = [
  { icon: DoorOpen, iconBg: 'bg-green-500/20 text-green-500', value: 24, label: 'Salles disponibles', subtext: 'Maintenant' },
  { icon: Users, iconBg: 'bg-orange-500/20 text-orange-500', value: 8, label: 'Salles occupées', subtext: 'Maintenant' },
  { icon: Monitor, iconBg: 'bg-blue-500/20 text-blue-400', value: 32, label: 'Équipements disponibles', subtext: 'Maintenant' },
  { icon: MonitorX, iconBg: 'bg-red-500/20 text-red-400', value: 6, label: 'Équipements occupés', subtext: 'Maintenant' },
  { icon: Calendar, iconBg: 'bg-primary/20 text-primary-light', value: 12, label: 'Réservations', subtext: "Aujourd'hui" },
]
</script>

<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
    <div v-for="stat in stats" :key="stat.label" class="bg-surface border border-border rounded-xl p-5 flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" :class="stat.iconBg">
        <component :is="stat.icon" class="w-6 h-6" />
      </div>
      <div class="min-w-0">
        <p class="text-foreground text-2xl font-bold">{{ stat.value }}</p>
        <p class="text-foreground text-sm">{{ stat.label }}</p>
        <p class="text-muted text-xs">{{ stat.subtext }}</p>
      </div>
    </div>
  </div>
</template>