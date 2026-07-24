<script setup lang="ts">
import { Calendar, DoorOpen, Users, Monitor } from 'lucide-vue-next'
import type { StatCardData } from '../types'

const stats: StatCardData[] = [
  {
    icon: Calendar,
    iconBg: 'bg-primary',
    label: "Réservations aujourd'hui",
    value: 24,
    trend: { value: '12%', positive: true },
  },
  {
    icon: DoorOpen,
    iconBg: 'bg-primary/70',
    label: 'Salles disponibles',
    value: 15,
    subtext: 'Sur 28 salles',
  },
  {
    icon: Users,
    iconBg: 'bg-primary',
    label: 'Utilisateurs actifs',
    value: 142,
    trend: { value: '8%', positive: true },
  },
  {
    icon: Monitor,
    iconBg: 'bg-primary/70',
    label: 'Équipements disponibles',
    value: 78,
    subtext: 'Sur 120 équipements',
  },
]
</script>

<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <StatCard
      v-for="stat in stats"
      :key="stat.label"
      :icon="stat.icon"
      :icon-bg="stat.iconBg"
      :label="stat.label"
      :value="stat.value"
      :trend="stat.trend"
      :subtext="stat.subtext"
    />
  </div>
</template>