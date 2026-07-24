<script setup lang="ts">
import { ChevronLeft, ChevronRight } from 'lucide-vue-next'
import { ref, computed } from 'vue'
import type { DayData } from '../types'

type ViewMode = 'week' | 'month'

const activeView = ref<ViewMode>('week')
const monthLabel = ref('Juillet 2026')

const days: DayData[] = [
  { date: 20, dayName: 'LUN', reservationCount: 5, bars: [1, 2, 1, 2, 1, 1, 0, 1], isSelected: false, isToday: false },
  { date: 21, dayName: 'MAR', reservationCount: 8, bars: [2, 3, 2, 3, 2, 2, 1, 2], isSelected: false, isToday: false },
  { date: 22, dayName: 'MER', reservationCount: 3, bars: [1, 1, 0, 1, 1, 0, 0, 1], isSelected: false, isToday: false },
  { date: 23, dayName: 'JEU', reservationCount: 12, bars: [3, 4, 3, 5, 4, 3, 2, 3], isSelected: true, isToday: true },
  { date: 24, dayName: 'VEN', reservationCount: 9, bars: [2, 3, 3, 2, 3, 2, 1, 2], isSelected: false, isToday: false },
  { date: 25, dayName: 'SAM', reservationCount: 1, bars: [0, 1, 0, 0, 0, 0, 0, 0], isSelected: false, isToday: false },
  { date: 26, dayName: 'DIM', reservationCount: 0, bars: [0, 0, 0, 0, 0, 0, 0, 0], isSelected: false, isToday: false },
]

const selectedDate = ref(23)

const barColorClass = (value: number) => {
  if (value === 0) return 'bg-muted/40'
  if (value <= 4) return 'bg-blue-500'
  if (value <= 8) return 'bg-orange-500'
  return 'bg-red-500'
}

const maxBarValue = computed(() =>
  Math.max(...days.flatMap(d => d.bars), 1)
)

const selectDay = (date: number) => {
  selectedDate.value = date
}
</script>

<template>
  <div class="bg-surface border border-border rounded-xl p-5">
    <!-- Onglets + navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-5">
      <div class="flex items-center gap-1 bg-background border border-border rounded-lg p-1 w-fit">
        <button
          class="px-3 py-1.5 rounded-md text-sm font-medium transition-colors"
          :class="activeView === 'week' ? 'bg-primary text-white' : 'text-muted hover:text-foreground'"
          @click="activeView = 'week'"
        >
          Semaine
        </button>
        <button
          class="px-3 py-1.5 rounded-md text-sm font-medium transition-colors"
          :class="activeView === 'month' ? 'bg-primary text-white' : 'text-muted hover:text-foreground'"
          @click="activeView = 'month'"
        >
          Mois
        </button>
      </div>

      <div class="flex items-center gap-2">
        <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-foreground hover:bg-border/50 transition-colors">
          <ChevronLeft class="w-4 h-4" />
        </button>
        <span class="bg-background border border-border rounded-lg px-3 py-1.5 text-foreground text-sm font-medium">
          {{ monthLabel }}
        </span>
        <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-foreground hover:bg-border/50 transition-colors">
          <ChevronRight class="w-4 h-4" />
        </button>
      </div>
    </div>

    <!-- Grille des jours -->
   <div class="grid grid-cols-7 gap-2 md:gap-4">
    <button
      v-for="day in days"
      :key="day.date"
      class="relative flex flex-col items-center text-center rounded-lg p-2 md:p-3 transition-colors min-w-0"
      :class="[
        selectedDate === day.date
          ? 'bg-primary/10 border border-primary/30 z-10 -mx-2 md:-mx-3'
          : 'border border-transparent hover:bg-border/30'
      ]"
      @click="selectDay(day.date)"
    >
      <span class="text-muted text-xs font-medium mb-1">{{ day.dayName }}</span>
      <span
        class="w-8 h-8 flex items-center justify-center rounded-full text-sm font-bold mb-1.5"
        :class="selectedDate === day.date ? 'bg-primary text-white' : 'text-foreground'"
      >
        {{ day.date }}
      </span>

      <div class="mb-2 leading-tight">
        <p class="text-foreground text-sm font-bold">{{ day.reservationCount }}</p>
        <p class="text-muted text-[10px] whitespace-nowrap">
          réservation{{ day.reservationCount !== 1 ? 's' : '' }}
        </p>
      </div>

      <div class="flex items-end gap-0.5 h-8 w-full justify-center">
        <div
          v-for="(bar, index) in day.bars"
          :key="index"
          class="w-1 rounded-sm transition-all"
          :class="barColorClass(bar)"
          :style="{ height: `${Math.max((bar / maxBarValue) * 100, 8)}%` }"
        />
      </div>
    </button>
  </div>

    <!-- Légende -->
    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 mt-5 pt-4 border-t border-border">
      <div class="flex items-center gap-1.5">
        <span class="w-2 h-2 rounded-full bg-muted/40" />
        <span class="text-muted text-xs">0</span>
      </div>
      <div class="flex items-center gap-1.5">
        <span class="w-2 h-2 rounded-full bg-blue-500" />
        <span class="text-muted text-xs">1-4</span>
      </div>
      <div class="flex items-center gap-1.5">
        <span class="w-2 h-2 rounded-full bg-orange-500" />
        <span class="text-muted text-xs">5-8</span>
      </div>
      <div class="flex items-center gap-1.5">
        <span class="w-2 h-2 rounded-full bg-red-500" />
        <span class="text-muted text-xs">+8</span>
      </div>
    </div>
  </div>
</template>