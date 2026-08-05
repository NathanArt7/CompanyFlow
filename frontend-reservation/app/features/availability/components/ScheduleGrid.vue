<script setup lang="ts">
import { computed } from 'vue'
import type { ScheduleRow } from '../types'

const props = defineProps<{
  rows: ScheduleRow[]
  columnLabel: string
  dayStartMinutes: number
  dayEndMinutes: number
  isOpen: boolean
  isLoading: boolean
  error: string | null
}>()

const hourLabels = computed(() => {
  const labels: string[] = []
  for (let m = props.dayStartMinutes; m < props.dayEndMinutes; m += 60) {
    labels.push(`${String(Math.floor(m / 60)).padStart(2, '0')}:${String(m % 60).padStart(2, '0')}`)
  }
  return labels
})

const statusBg: Record<string, string> = {
  available: 'bg-green-600/80',
  reserved: 'bg-blue-600',
  buffer: 'bg-yellow-500',
  maintenance: 'bg-orange-600',
  out_of_service: 'bg-red-900/60',
}

function blockStyle(startMinutes: number, endMinutes: number) {
  const total = props.dayEndMinutes - props.dayStartMinutes
  const left = Math.max(0, ((startMinutes - props.dayStartMinutes) / total) * 100)
  const width = Math.min(100 - left, ((endMinutes - startMinutes) / total) * 100)
  return { left: `${left}%`, width: `${width}%` }
}

function formatHour(minutes: number): string {
  return `${String(Math.floor(minutes / 60)).padStart(2, '0')}h${String(minutes % 60).padStart(2, '0')}`
}

function formatRange(startMinutes: number, endMinutes: number): string {
  return `${formatHour(startMinutes)} ~ ${formatHour(endMinutes)}`
}
</script>

<template>
  <div class="bg-surface border border-border rounded-xl overflow-hidden">
    <p v-if="isLoading" class="text-muted text-xs p-5">
      Chargement…
    </p>
    <p v-else-if="error" class="text-red-400 text-xs p-5">
      {{ error }}
    </p>
    <p v-else-if="!isOpen" class="text-muted text-xs p-5">
      L'entreprise est fermée ce jour-là — aucun horaire de service configuré.
    </p>
    <p v-else-if="rows.length === 0" class="text-muted text-xs p-5">
      Aucune donnée à afficher.
    </p>

    <div v-else class="overflow-x-auto">
      <div class="min-w-[900px]">
        <div class="grid border-b border-grid-line" :style="{ gridTemplateColumns: '240px 1fr' }">
          <div class="px-4 py-3 text-foreground text-sm font-semibold">{{ columnLabel }}</div>
          <div class="grid" :style="{ gridTemplateColumns: `repeat(${hourLabels.length}, 1fr)` }">
            <div
              v-for="label in hourLabels"
              :key="label"
              class="px-2 py-3 text-center text-muted text-xs font-medium border-l border-grid-line"
            >
              {{ label }}
            </div>
          </div>
        </div>

        <div
          v-for="row in rows"
          :key="row.id"
          class="grid border-b border-grid-line last:border-0"
          :style="{ gridTemplateColumns: '240px 1fr' }"
        >
          <div class="flex items-center gap-2.5 px-4 py-3">
            <component :is="row.icon" class="w-4 h-4 text-muted shrink-0" />
            <div class="min-w-0">
              <p class="text-foreground text-sm font-medium truncate">{{ row.name }}</p>
              <p class="text-muted text-xs truncate">{{ row.subtitle }}</p>
            </div>
          </div>

          <div class="relative grid" :style="{ gridTemplateColumns: `repeat(${hourLabels.length}, 1fr)` }">
            <div
              v-for="label in hourLabels"
              :key="label"
              class="h-16 border-l border-grid-line bg-green-600/20"
            />

            <div
              v-if="row.fullDayStatus"
              class="absolute inset-y-0 left-0 w-full h-16 flex items-center justify-center"
              :class="statusBg[row.fullDayStatus]"
            >
              <span class="text-white text-xs font-medium">{{ row.fullDayLabel }}</span>
            </div>

            <button
              v-for="(block, index) in row.blocks"
              v-else
              :key="index"
              class="absolute top-0 h-16 transition-opacity hover:opacity-90"
              :class="statusBg[block.status]"
              :style="blockStyle(block.startMinutes, block.endMinutes)"
              :title="`${block.title} (${formatRange(block.startMinutes, block.endMinutes)})`"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
