<script setup lang="ts">
import { Users } from 'lucide-vue-next'
import { computed } from 'vue'
import type { ScheduleBlock, ScheduleRow, SlotStatus } from '../types'
import { buildBufferBlocks } from '../scheduleBuffer'
import type { RawRoom } from '~/features/rooms/type'
import type { RawReservation } from '~/features/reservations/type'

const props = defineProps<{
  rooms: RawRoom[]
  reservations: RawReservation[]
  dayStartMinutes: number
  dayEndMinutes: number
  bufferMinutes: number
  isOpen: boolean
  isLoading: boolean
  error: string | null
}>()

function timeToMinutes(value: string): number {
  const [h, m] = value.split(':').map(Number)
  return (h ?? 0) * 60 + (m ?? 0)
}

const fullDayStatusMap: Record<string, { status: SlotStatus, label: string }> = {
  'En maintenance': { status: 'maintenance', label: 'En maintenance' },
  'Hors service': { status: 'out_of_service', label: 'Hors service' },
}

const rows = computed<ScheduleRow[]>(() => props.rooms.map((room) => {
  const fullDay = fullDayStatusMap[room.statut.value]

  const reservedBlocks: ScheduleBlock[] = fullDay
    ? []
    : props.reservations
      .filter(r => r.room_id === room.id && r.statut === 'CONFIRMEE')
      .map(r => ({
        startMinutes: timeToMinutes(r.heure_debut),
        endMinutes: timeToMinutes(r.heure_fin),
        status: 'reserved',
        title: r.motif,
      }))

  const blocks: ScheduleBlock[] = fullDay
    ? []
    : [
        ...buildBufferBlocks(reservedBlocks, props.bufferMinutes, props.dayEndMinutes),
        ...reservedBlocks,
      ]

  return {
    id: String(room.id),
    name: room.nom,
    subtitle: `Réunion • ${room.capacite ?? '—'} places`,
    icon: Users,
    blocks,
    fullDayStatus: fullDay?.status,
    fullDayLabel: fullDay?.label,
  }
}))
</script>

<template>
  <div>
    <h2 class="text-foreground text-xl font-bold mb-3">Disponibilités des salles</h2>

    <AvailabilityLegend class="mb-4" />

    <ScheduleGrid
      :rows="rows"
      column-label="Salles"
      :day-start-minutes="dayStartMinutes"
      :day-end-minutes="dayEndMinutes"
      :is-open="isOpen"
      :is-loading="isLoading"
      :error="error"
    />
  </div>
</template>
