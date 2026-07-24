<script setup lang="ts">
import { Building2, CheckCircle2, Users, Wrench } from 'lucide-vue-next'
import type { RoomStat } from '../types'

const stats: RoomStat[] = [
  { icon: Building2, iconBg: 'bg-primary/20 text-primary-light', value: 24, label: 'Total des salles', subtext: 'Toutes catégories confondues' },
  { icon: CheckCircle2, iconBg: 'bg-green-500/20 text-green-500', value: 18, label: 'Disponibles', subtext: '75% du total' },
  { icon: Users, iconBg: 'bg-orange-500/20 text-orange-500', value: 4, label: 'Occupées', subtext: '17% du total' },
  { icon: Wrench, iconBg: 'bg-red-500/20 text-red-400', value: 2, label: 'En maintenance', subtext: '8% du total' },
]
</script>

<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div v-for="stat in stats" :key="stat.label" class="bg-surface border border-border rounded-xl p-5 flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" :class="stat.iconBg">
        <component :is="stat.icon" class="w-6 h-6" />
      </div>
      <div class="min-w-0">
        <p class="text-foreground text-2xl font-bold">{{ stat.value }}</p>
        <p class="text-foreground text-sm">{{ stat.label }}</p>
        <p class="text-muted text-xs truncate">{{ stat.subtext }}</p>
      </div>
    </div>
  </div>
</template>