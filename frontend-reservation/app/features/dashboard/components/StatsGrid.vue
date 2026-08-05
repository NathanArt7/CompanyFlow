<script setup lang="ts">
import { Calendar, DoorOpen, Users, Monitor } from 'lucide-vue-next'
import { onMounted, ref } from 'vue'
import type { StatCardData } from '../type'
import { useDashboardService } from '../services/dashboard.service'

const dashboardService = useDashboardService()
const isLoading = ref(true)
const error = ref<string | null>(null)
const stats = ref<StatCardData[]>([])

onMounted(async () => {
  try {
    const data = await dashboardService.getStats()
    stats.value = [
      {
        icon: Calendar,
        iconBg: 'bg-primary',
        label: "Réservations aujourd'hui",
        value: data.reservations_today,
      },
      {
        icon: DoorOpen,
        iconBg: 'bg-primary/70',
        label: 'Salles disponibles',
        value: data.rooms.available,
        subtext: `Sur ${data.rooms.total} salles`,
      },
      {
        icon: Users,
        iconBg: 'bg-primary',
        label: 'Utilisateurs actifs',
        value: data.active_users,
      },
      {
        icon: Monitor,
        iconBg: 'bg-primary/70',
        label: 'Équipements disponibles',
        value: data.equipments.available,
        subtext: `Sur ${data.equipments.total} équipements`,
      },
    ]
  } catch {
    error.value = 'Impossible de charger les statistiques.'
  } finally {
    isLoading.value = false
  }
})
</script>

<template>
  <div v-if="isLoading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <SkeletonStatCard v-for="i in 4" :key="i" />
  </div>
  <p v-else-if="error" class="text-red-400 text-xs">
    {{ error }}
  </p>
  <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
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