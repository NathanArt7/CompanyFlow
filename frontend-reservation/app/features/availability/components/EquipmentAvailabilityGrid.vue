<script setup lang="ts">
import { Monitor } from 'lucide-vue-next'
import { computed } from 'vue'
import type { ScheduleBlock, ScheduleRow, SlotStatus } from '../types'
import { buildBufferBlocks } from '../scheduleBuffer'
import type { RawEquipment } from '~/features/equipments/type'
import type { RawReservation } from '~/features/reservations/type'

const props = defineProps<{
  equipments: RawEquipment[]
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
  EN_PANNE: { status: 'out_of_service', label: 'En panne' },
  EN_MAINTENANCE: { status: 'maintenance', label: 'En maintenance' },
  HORS_SERVICE: { status: 'out_of_service', label: 'Hors service' },
}

const rows = computed<ScheduleRow[]>(() => props.equipments.map((equipment) => {
  const fullDay = fullDayStatusMap[equipment.etat]

  const reservedBlocks: ScheduleBlock[] = fullDay
    ? []
    : props.reservations
      .filter(r => r.statut === 'CONFIRMEE' && r.equipments.some(eq => eq.id === equipment.id))
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
    id: String(equipment.id),
    name: equipment.nom,
    subtitle: equipment.category?.nom ?? '—',
    icon: Monitor,
    blocks,
    fullDayStatus: fullDay?.status,
    fullDayLabel: fullDay?.label,
  }
}))
</script>

<template>
  <div>
    <h2 class="text-foreground text-xl font-bold mb-3">Disponibilité des équipements</h2>

    <AvailabilityLegend class="mb-4" />

    <ScheduleGrid
      :rows="rows"
      column-label="Équipements"
      :day-start-minutes="dayStartMinutes"
      :day-end-minutes="dayEndMinutes"
      :is-open="isOpen"
      :is-loading="isLoading"
      :error="error"
    />
  </div>
</template>
