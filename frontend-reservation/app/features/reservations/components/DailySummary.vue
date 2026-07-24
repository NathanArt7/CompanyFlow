<script setup lang="ts">
import { Calendar, Building2, CalendarX, Monitor, ChevronDown } from 'lucide-vue-next'
import type { SummaryCard } from '../types'

const cards: SummaryCard[] = [
  {
    icon: Calendar,
    iconBg: 'bg-primary',
    value: 12,
    label: 'Réservations',
    barColor: 'bg-primary',
    percentage: 100,
    percentageLabel: '100% du total',
  },
  {
    icon: Building2,
    iconBg: 'bg-blue-500',
    value: 8,
    label: 'Salles occupées',
    barColor: 'bg-blue-500',
    percentage: 67,
    percentageLabel: '67% du total',
  },
  {
    icon: CalendarX,
    iconBg: 'bg-red-500',
    value: 2,
    label: 'Annulées',
    barColor: 'bg-red-500',
    percentage: 17,
    percentageLabel: '17% du total',
  },
  {
    icon: Monitor,
    iconBg: 'bg-green-500',
    value: 15,
    label: 'Équipements réservés',
    barColor: 'bg-green-500',
    percentage: 100,
    percentageLabel: '125% de la capacité',
  },
]
</script>

<template>
  <div class="bg-surface border border-border rounded-xl p-5">
    <div class="flex items-center justify-between mb-5">
      <h2 class="text-foreground font-semibold">Résumé de la journée</h2>
      <button class="flex items-center gap-1.5 bg-background border border-border rounded-lg px-3 py-1.5 text-foreground text-xs">
        <Calendar class="w-3.5 h-3.5 text-muted" />
        Jeudi 23 juillet 2026
      </button>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <div v-for="card in cards" :key="card.label">
        <div
          class="w-10 h-10 rounded-lg flex items-center justify-center mb-3"
          :class="card.iconBg"
        >
          <component :is="card.icon" class="w-5 h-5 text-white" />
        </div>

        <p class="text-foreground text-2xl md:text-3xl font-bold">{{ card.value }}</p>
        <p class="text-muted text-sm mt-0.5">{{ card.label }}</p>

        <div class="w-full h-1 bg-background rounded-full overflow-hidden mt-3 mb-1.5">
          <div
            class="h-full rounded-full"
            :class="card.barColor"
            :style="{ width: `${Math.min(card.percentage, 100)}%` }"
          />
        </div>
        <p class="text-muted text-xs">{{ card.percentageLabel }}</p>
      </div>
    </div>
  </div>
</template>