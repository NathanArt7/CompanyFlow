<script setup lang="ts">
import { ChevronLeft, ChevronRight } from 'lucide-vue-next'
import { ref, computed } from 'vue'

type DayStatus = 'reserved' | 'partial' | 'available'

interface CalendarDay {
  date: number
  isCurrentMonth: boolean
  isSelected: boolean
  status?: DayStatus
}

const weekDays = ['LUN', 'MAR', 'MER', 'JEU', 'VEN', 'SAM', 'DIM']

const today = new Date()
const viewDate = ref(new Date(today.getFullYear(), today.getMonth(), 1))
const selectedDate = ref(new Date(today.getFullYear(), today.getMonth(), today.getDate()))

const monthLabel = computed(() =>
  new Intl.DateTimeFormat('fr-FR', { month: 'long', year: 'numeric' })
    .format(viewDate.value)
    .replace(/^./, c => c.toUpperCase())
)

const dayStatuses: Record<number, DayStatus> = {
  3: 'reserved',
  8: 'partial',
  14: 'reserved',
  16: 'partial',
  22: 'reserved',
  27: 'available',
}

const calendarDays = computed<CalendarDay[]>(() => {
  const year = viewDate.value.getFullYear()
  const month = viewDate.value.getMonth()

  const firstDayOfMonth = new Date(year, month, 1)
  const startOffset = (firstDayOfMonth.getDay() + 6) % 7

  const daysInMonth = new Date(year, month + 1, 0).getDate()
  const daysInPrevMonth = new Date(year, month, 0).getDate()

  const days: CalendarDay[] = []

  for (let i = startOffset - 1; i >= 0; i--) {
    days.push({
      date: daysInPrevMonth - i,
      isCurrentMonth: false,
      isSelected: false,
    })
  }

  for (let d = 1; d <= daysInMonth; d++) {
    const isSelected =
      d === selectedDate.value.getDate() &&
      month === selectedDate.value.getMonth() &&
      year === selectedDate.value.getFullYear()

    days.push({
      date: d,
      isCurrentMonth: true,
      isSelected,
      status: dayStatuses[d],
    })
  }

  const remaining = (7 - (days.length % 7)) % 7
  for (let d = 1; d <= remaining; d++) {
    days.push({
      date: d,
      isCurrentMonth: false,
      isSelected: false,
    })
  }

  return days
})

const goToPrevMonth = () => {
  viewDate.value = new Date(viewDate.value.getFullYear(), viewDate.value.getMonth() - 1, 1)
}

const goToNextMonth = () => {
  viewDate.value = new Date(viewDate.value.getFullYear(), viewDate.value.getMonth() + 1, 1)
}

const selectDay = (day: CalendarDay) => {
  if (!day.isCurrentMonth) return
  selectedDate.value = new Date(viewDate.value.getFullYear(), viewDate.value.getMonth(), day.date)
}

const statusDotClass: Record<DayStatus, string> = {
  reserved: 'bg-primary',
  partial: 'bg-orange-500',
  available: 'bg-muted',
}
</script>

<template>
  <div>
    <!-- En-tête : mois + navigation -->
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-foreground font-semibold text-sm">{{ monthLabel }}</h3>
      <div class="flex items-center gap-1">
        <button
          class="w-6 h-6 flex items-center justify-center rounded-md text-muted hover:text-foreground hover:bg-border/50 transition-colors"
          @click="goToPrevMonth"
        >
          <ChevronLeft class="w-4 h-4" />
        </button>
        <button
          class="w-6 h-6 flex items-center justify-center rounded-md text-muted hover:text-foreground hover:bg-border/50 transition-colors"
          @click="goToNextMonth"
        >
          <ChevronRight class="w-4 h-4" />
        </button>
      </div>
    </div>

    <!-- Jours de la semaine -->
    <div class="grid grid-cols-7 mb-2">
      <span
        v-for="day in weekDays"
        :key="day"
        class="text-muted text-xs font-medium text-center"
      >
        {{ day }}
      </span>
    </div>

    <!-- Grille des jours -->
    <div class="grid grid-cols-7 gap-y-1">
      <button
        v-for="(day, index) in calendarDays"
        :key="index"
        class="relative w-8 h-8 mx-auto flex items-center justify-center rounded-lg text-xs transition-colors"
        :class="[
          day.isCurrentMonth ? 'text-foreground hover:bg-border/50' : 'text-muted/40 cursor-default',
          day.isSelected ? 'bg-primary text-white hover:bg-primary' : ''
        ]"
        @click="selectDay(day)"
      >
        {{ day.date }}
        <span
          v-if="day.status && !day.isSelected"
          class="absolute bottom-0.5 w-1 h-1 rounded-full"
          :class="statusDotClass[day.status]"
        />
      </button>
    </div>

    <!-- Légende -->
    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 mt-5 pt-4 border-t border-border">
      <div class="flex items-center gap-1.5">
        <span class="w-2 h-2 rounded-full bg-primary" />
        <span class="text-muted text-xs">Réservé</span>
      </div>
      <div class="flex items-center gap-1.5">
        <span class="w-2 h-2 rounded-full bg-orange-500" />
        <span class="text-muted text-xs">Partiellement réservé</span>
      </div>
      <div class="flex items-center gap-1.5">
        <span class="w-2 h-2 rounded-full bg-muted" />
        <span class="text-muted text-xs">Disponible</span>
      </div>
    </div>
  </div>
</template>