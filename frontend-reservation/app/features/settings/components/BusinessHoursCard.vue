<script setup lang="ts">
import { Clock } from 'lucide-vue-next'
import type { BusinessHourRow } from '../types'

const hours: BusinessHourRow[] = [
  { day: 'Lundi', hours: '08:00 - 18:00', isClosed: false },
  { day: 'Mardi', hours: '08:00 - 18:00', isClosed: false },
  { day: 'Mercredi', hours: '08:00 - 18:00', isClosed: false },
  { day: 'Jeudi', hours: '08:00 - 18:00', isClosed: false },
  { day: 'Vendredi', hours: '08:00 - 18:00', isClosed: false },
  { day: 'Samedi', hours: 'Fermé', isClosed: true },
  { day: 'Dimanche', hours: 'Fermé', isClosed: true },
]
</script>

<template>
  <div class="bg-surface border border-border rounded-xl p-5">
    <div class="flex items-start justify-between mb-4">
      <div class="flex items-start gap-3">
        <div class="w-9 h-9 rounded-lg bg-primary/15 flex items-center justify-center shrink-0">
          <Clock class="w-4.5 h-4.5 text-primary-light" />
        </div>
        <div>
          <h2 class="text-foreground font-semibold">Horaires de service</h2>
          <p class="text-muted text-xs mt-0.5">Définissez les horaires de disponibilité pour les réservations.</p>
        </div>
      </div>
      <NuxtLink
        to="/settings/hours"
        class="bg-background border border-border rounded-lg px-3 py-1.5 text-sm text-foreground hover:border-primary/40 transition-colors shrink-0"
      >
        Gérer
      </NuxtLink>
    </div>

    <div class="space-y-2.5">
      <div v-for="row in hours" :key="row.day" class="flex items-center justify-between text-sm">
        <div class="flex items-center gap-2">
          <Clock class="w-3.5 h-3.5" :class="row.isClosed ? 'text-muted/50' : 'text-muted'" />
          <span class="text-foreground">{{ row.day }}</span>
        </div>
        <span :class="row.isClosed ? 'text-red-400' : 'text-foreground'">{{ row.hours }}</span>
      </div>
    </div>
  </div>
</template>