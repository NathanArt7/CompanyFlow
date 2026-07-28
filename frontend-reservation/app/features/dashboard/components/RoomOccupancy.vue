<script setup lang="ts">
import { LineChart } from 'lucide-vue-next'
import { onMounted, ref } from 'vue'
import type { RoomOccupancy } from '../type'
import { useDashboardService } from '../services/dashboard.service'

const dashboardService = useDashboardService()
const isLoading = ref(true)
const error = ref<string | null>(null)
const rooms = ref<RoomOccupancy[]>([])

onMounted(async () => {
  try {
    const today = await dashboardService.getTodayAvailability()

    rooms.value = (today?.salles ?? [])
      .filter(salle => salle.creneaux.length > 0)
      .map((salle) => {
        const total = salle.creneaux.length
        const occupied = salle.creneaux.filter(c => c.statut === 'OCCUPE').length
        return { name: salle.nom, percentage: Math.round((occupied / total) * 100) }
      })
  } catch {
    error.value = "Impossible de charger l'occupation des salles."
  } finally {
    isLoading.value = false
  }
})
</script>

<template>
  <div class="bg-surface border border-border rounded-xl p-4">
    <div class="flex items-center justify-between mb-0.5">
      <h2 class="text-foreground font-semibold text-sm">Occupation des salles</h2>
      <button class="w-7 h-7 flex items-center justify-center rounded-lg bg-background border border-border text-muted hover:text-foreground transition-colors">
        <LineChart class="w-3.5 h-3.5" />
      </button>
    </div>
    <p class="text-muted text-xs mb-3">Aujourd'hui</p>

    <p v-if="isLoading" class="text-muted text-xs">
      Chargement…
    </p>
    <p v-else-if="error" class="text-red-400 text-xs">
      {{ error }}
    </p>
    <p v-else-if="rooms.length === 0" class="text-muted text-xs">
      Aucune salle disponible aujourd'hui.
    </p>
    <div v-else class="space-y-2">
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