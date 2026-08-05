<script setup lang="ts">
import { DoorOpen, Users, Monitor, MonitorX, Calendar } from 'lucide-vue-next'
import { ref, onMounted } from 'vue'
import type { AvailabilityStat } from '../types'
import { useRoomService } from '~/features/rooms/services/room.service'
import { useReservationsService } from '~/features/reservations/services/reservations.service'
import { useDashboardService } from '~/features/dashboard/services/dashboard.service'

const props = defineProps<{
  hideOccupied?: boolean
}>()

const roomService = useRoomService()
const reservationsService = useReservationsService()
const dashboardService = useDashboardService()

const isLoading = ref(true)
const error = ref<string | null>(null)
const stats = ref<AvailabilityStat[]>([])

function todayIso(): string {
  const d = new Date()
  return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`
}

function timeToMinutes(value: string): number {
  const [h, m] = value.split(':').map(Number)
  return (h ?? 0) * 60 + (m ?? 0)
}

function timeString(date: Date): string {
  return `${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}`
}

async function load() {
  isLoading.value = true
  error.value = null
  try {
    const now = new Date()
    const nowStr = timeString(now)
    const nowMinutes = timeToMinutes(nowStr)
    const oneMinuteLater = timeString(new Date(now.getTime() + 60_000))

    // "Disponible/occupée maintenant" est calculé à partir des réservations confirmées qui
    // chevauchent l'instant présent (via /availability/week et /availability/equipments),
    // et non depuis les colonnes statiques statut/etat qui ne reflètent qu'un état géré
    // manuellement (maintenance, hors service...), jamais l'agenda du jour.
    const [rooms, todayAvailability, availableEquipments, dailySummary] = await Promise.all([
      roomService.list(),
      dashboardService.getTodayAvailability(),
      reservationsService.getAvailableEquipments(todayIso(), nowStr, oneMinuteLater),
      reservationsService.getDailySummary(todayIso()),
    ])

    const meetingRooms = rooms.filter(room => room.type.value === 'MEETING')
    const roomSlotsById = new Map((todayAvailability?.salles ?? []).map(salle => [salle.id, salle.creneaux]))

    let roomsAvailable = 0
    let roomsOccupied = 0

    for (const room of meetingRooms) {
      if (room.statut.value === 'En maintenance' || room.statut.value === 'Hors service') continue

      const occupiedNow = (roomSlotsById.get(room.id) ?? []).some(slot =>
        slot.statut === 'OCCUPE'
        && timeToMinutes(slot.debut) <= nowMinutes
        && nowMinutes < timeToMinutes(slot.fin),
      )

      if (occupiedNow) roomsOccupied++
      else roomsAvailable++
    }

    const equipmentsAvailable = availableEquipments.filter(eq => eq.disponible).length
    const equipmentsOccupied = availableEquipments.length - equipmentsAvailable

    stats.value = [
      { icon: DoorOpen, iconBg: 'bg-green-500/20 text-green-500', value: roomsAvailable, label: 'Salles de réunion disponibles', subtext: 'Maintenant' },
      ...(props.hideOccupied ? [] : [{ icon: Users, iconBg: 'bg-orange-500/20 text-orange-500', value: roomsOccupied, label: 'Salles de réunion occupées', subtext: 'Maintenant' }]),
      { icon: Monitor, iconBg: 'bg-blue-500/20 text-blue-400', value: equipmentsAvailable, label: 'Équipements empruntables disponibles', subtext: 'Maintenant' },
      ...(props.hideOccupied ? [] : [{ icon: MonitorX, iconBg: 'bg-cyan-500/20 text-cyan-400', value: equipmentsOccupied, label: 'Équipements empruntables occupés', subtext: 'Maintenant' }]),
      { icon: Calendar, iconBg: 'bg-primary/20 text-primary-light', value: dailySummary.reservations.total, label: 'Réservations', subtext: "Aujourd'hui" },
    ]
  } catch (e) {
    error.value = (e as { message?: string }).message ?? 'Impossible de charger les statistiques.'
  } finally {
    isLoading.value = false
  }
}

onMounted(load)

defineExpose({ refresh: load })
</script>

<template>
  <div v-if="isLoading" class="grid grid-cols-1 sm:grid-cols-2 gap-4" :class="hideOccupied ? 'lg:grid-cols-3' : 'lg:grid-cols-5'">
    <SkeletonStatCard v-for="i in (hideOccupied ? 3 : 5)" :key="i" />
  </div>
  <p v-else-if="error" class="text-red-400 text-xs">
    {{ error }}
  </p>
  <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-4" :class="hideOccupied ? 'lg:grid-cols-3' : 'lg:grid-cols-5'">
    <div v-for="stat in stats" :key="stat.label" class="bg-surface border border-border rounded-xl p-5">
      <div class="w-11 h-11 rounded-xl flex items-center justify-center mb-3" :class="stat.iconBg">
        <component :is="stat.icon" class="w-5 h-5" />
      </div>
      <p class="text-foreground text-2xl font-bold">{{ stat.value }}</p>
      <p class="text-foreground text-sm">{{ stat.label }}</p>
      <p class="text-muted text-xs mt-1">{{ stat.subtext }}</p>
    </div>
  </div>
</template>
