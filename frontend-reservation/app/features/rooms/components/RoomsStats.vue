<script setup lang="ts">
import { Building2, DoorOpen, Archive, Users, Wrench } from 'lucide-vue-next'
import { ref, onMounted } from 'vue'
import type { RoomStat } from '../types'
import { useRoomService } from '../services/room.service'

const roomService = useRoomService()

const isLoading = ref(true)
const error = ref<string | null>(null)
const stats = ref<RoomStat[]>([])

function pct(part: number, total: number): number {
  return total > 0 ? Math.round((part / total) * 100) : 0
}

async function load() {
  isLoading.value = true
  error.value = null
  try {
    const data = await roomService.getStats()

    stats.value = [
      {
        icon: Building2,
        iconBg: 'bg-primary/20 text-primary-light',
        value: data.total,
        label: 'Total des salles',
        subtext: 'Toutes catégories confondues',
        barColor: '',
        percentage: 0,
      },
      {
        icon: DoorOpen,
        iconBg: 'bg-blue-500/20 text-blue-400',
        value: data.meeting_rooms,
        label: 'Salles de réunion',
        subtext: `${pct(data.meeting_rooms, data.total)}% du total`,
        barColor: 'bg-blue-500',
        percentage: pct(data.meeting_rooms, data.total),
      },
      {
        icon: Archive,
        iconBg: 'bg-purple-500/20 text-purple-400',
        value: data.storage_rooms,
        label: 'Salles de stockage',
        subtext: `${pct(data.storage_rooms, data.total)}% du total`,
        barColor: 'bg-purple-500',
        percentage: pct(data.storage_rooms, data.total),
      },
      {
        icon: Users,
        iconBg: 'bg-orange-500/20 text-orange-500',
        value: data.meeting_rooms_occupied,
        label: 'Salles de réunion occupées',
        subtext: `${pct(data.meeting_rooms_occupied, data.meeting_rooms)}% des salles de réunion`,
        barColor: 'bg-orange-500',
        percentage: pct(data.meeting_rooms_occupied, data.meeting_rooms),
      },
      {
        icon: Wrench,
        iconBg: 'bg-red-500/20 text-red-400',
        value: data.in_maintenance,
        label: 'En maintenance',
        subtext: `${pct(data.in_maintenance, data.total)}% du total`,
        barColor: 'bg-red-500',
        percentage: pct(data.in_maintenance, data.total),
      },
    ]
  } catch {
    error.value = 'Impossible de charger les statistiques.'
  } finally {
    isLoading.value = false
  }
}

onMounted(load)

defineExpose({
  refresh: load,
})
</script>

<template>
  <div v-if="isLoading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
    <SkeletonStatCard v-for="i in 5" :key="i" with-bar />
  </div>
  <p v-else-if="error" class="text-red-400 text-xs">
    {{ error }}
  </p>
  <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
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
