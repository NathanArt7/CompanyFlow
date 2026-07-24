<script setup lang="ts">
import { LineChart } from 'lucide-vue-next'
import type { RoomOccupancy } from '../types'

const rooms: RoomOccupancy[] = [
  { name: 'Salle Alpha', percentage: 92 },
  { name: 'Salle Bêta', percentage: 76 },
  { name: 'Salle Gamma', percentage: 48 },
  { name: 'Salle Delta', percentage: 19 },
]
</script>

<template>
  <div class="bg-surface border border-border rounded-xl p-4">
    <div class="flex items-center justify-between mb-0.5">
      <h2 class="text-foreground font-semibold text-sm">Occupation des salles</h2>
      <button class="w-7 h-7 flex items-center justify-center rounded-lg bg-background border border-border text-muted hover:text-foreground transition-colors">
        <LineChart class="w-3.5 h-3.5" />
      </button>
    </div>
    <p class="text-muted text-xs mb-3">Basé sur les 7 derniers jours</p>

    <div class="space-y-2">
      <div v-for="room in rooms" :key="room.name">
        <div class="flex items-center justify-between mb-0.5">
          <span class="text-foreground text-xs">{{ room.name }}</span>
          <span class="text-muted text-xs">{{ room.percentage }}%</span>
        </div>
        <div class="w-full h-1 bg-background rounded-full overflow-hidden">
          <div
            class="h-full rounded-full bg-gradient-to-r from-primary to-purple-400"
            :style="{ width: `${room.percentage}%` }"
          />
        </div>
      </div>
    </div>
  </div>
</template>