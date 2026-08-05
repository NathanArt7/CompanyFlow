<script setup lang="ts">
import { ChevronLeft, ChevronRight } from 'lucide-vue-next'
import { ref, computed, watch } from 'vue'
import type { MonthDayData, WeekDayData } from '../type'
import { useReservationsService } from '../services/reservations.service'

const props = defineProps<{
  selectedDate: string
}>()

const emit = defineEmits<{
  'update:selectedDate': [string]
}>()

const reservationsService = useReservationsService()
const authStore = useAuthStore()

// Le Super Employé n'a qu'une vision de la semaine en cours, sans navigation ni vue mensuelle.
const isSuperEmploye = computed(() => authStore.user?.role === 'Super_Employe')

type ViewMode = 'week' | 'month'

const activeView = ref<ViewMode>('week')
const isLoading = ref(true)
const error = ref<string | null>(null)
const counts = ref<Record<string, number>>({})

const weekDayNames = ['LUN', 'MAR', 'MER', 'JEU', 'VEN', 'SAM', 'DIM']

function parseIsoLocal(iso: string): Date {
  const [year, month, day] = iso.split('-').map(Number)
  return new Date(year, month - 1, day)
}

function toIso(date: Date): string {
  return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`
}

function addDays(date: Date, amount: number): Date {
  const copy = new Date(date)
  copy.setDate(copy.getDate() + amount)
  return copy
}

function isSameDay(a: Date, b: Date): boolean {
  return a.getFullYear() === b.getFullYear() && a.getMonth() === b.getMonth() && a.getDate() === b.getDate()
}

const viewDate = ref(parseIsoLocal(props.selectedDate))
const today = new Date()

const weekStart = computed(() => {
  const offset = (viewDate.value.getDay() + 6) % 7
  return addDays(viewDate.value, -offset)
})

const weekDays = computed<WeekDayData[]>(() => {
  return Array.from({ length: 7 }, (_, i) => {
    const date = addDays(weekStart.value, i)
    const iso = toIso(date)
    return {
      iso,
      dayNumber: date.getDate(),
      dayName: weekDayNames[i],
      count: counts.value[iso] ?? 0,
      isSelected: iso === props.selectedDate,
      isToday: isSameDay(date, today),
    }
  })
})

const maxWeekCount = computed(() => Math.max(...weekDays.value.map(d => d.count), 1))

const monthDays = computed<MonthDayData[]>(() => {
  const year = viewDate.value.getFullYear()
  const month = viewDate.value.getMonth()

  const firstDayOfMonth = new Date(year, month, 1)
  const startOffset = (firstDayOfMonth.getDay() + 6) % 7

  const daysInMonth = new Date(year, month + 1, 0).getDate()
  const daysInPrevMonth = new Date(year, month, 0).getDate()

  const days: MonthDayData[] = []

  for (let i = startOffset - 1; i >= 0; i--) {
    const date = new Date(year, month - 1, daysInPrevMonth - i)
    days.push({ iso: toIso(date), dayNumber: date.getDate(), isCurrentMonth: false, isSelected: false, isToday: false, count: null })
  }

  for (let d = 1; d <= daysInMonth; d++) {
    const date = new Date(year, month, d)
    const iso = toIso(date)
    days.push({
      iso,
      dayNumber: d,
      isCurrentMonth: true,
      isSelected: iso === props.selectedDate,
      isToday: isSameDay(date, today),
      count: counts.value[iso] ?? 0,
    })
  }

  const remaining = (7 - (days.length % 7)) % 7
  for (let d = 1; d <= remaining; d++) {
    const date = new Date(year, month + 1, d)
    days.push({ iso: toIso(date), dayNumber: d, isCurrentMonth: false, isSelected: false, isToday: false, count: null })
  }

  return days
})

const periodLabel = computed(() => {
  const reference = activeView.value === 'week' ? weekStart.value : viewDate.value
  const formatted = new Intl.DateTimeFormat('fr-FR', { month: 'long', year: 'numeric' }).format(reference)
  return formatted.charAt(0).toUpperCase() + formatted.slice(1).replace('.', '')
})

const countColorClass = (count: number | null) => {
  if (count === null) return ''
  if (count === 0) return 'bg-muted/40'
  if (count <= 4) return 'bg-blue-500'
  if (count <= 8) return 'bg-orange-500'
  return 'bg-red-500'
}

async function fetchCounts() {
  isLoading.value = true
  error.value = null
  try {
    let from: Date
    let to: Date

    if (activeView.value === 'week') {
      from = weekStart.value
      to = addDays(weekStart.value, 6)
    } else {
      const year = viewDate.value.getFullYear()
      const month = viewDate.value.getMonth()
      from = new Date(year, month, 1)
      to = new Date(year, month + 1, 0)
    }

    counts.value = await reservationsService.getCounts(toIso(from), toIso(to))
  } catch {
    error.value = 'Impossible de charger le calendrier.'
    counts.value = {}
  } finally {
    isLoading.value = false
  }
}

watch([activeView, viewDate], fetchCounts, { immediate: true })
watch(() => props.selectedDate, (iso) => {
  const parsed = parseIsoLocal(iso)
  if (parsed.getMonth() !== viewDate.value.getMonth() || parsed.getFullYear() !== viewDate.value.getFullYear()) {
    viewDate.value = parsed
  }
})

const goPrev = () => {
  viewDate.value = activeView.value === 'week'
    ? addDays(viewDate.value, -7)
    : new Date(viewDate.value.getFullYear(), viewDate.value.getMonth() - 1, 1)
}

const goNext = () => {
  viewDate.value = activeView.value === 'week'
    ? addDays(viewDate.value, 7)
    : new Date(viewDate.value.getFullYear(), viewDate.value.getMonth() + 1, 1)
}

const selectWeekDay = (day: WeekDayData) => {
  emit('update:selectedDate', day.iso)
}

const selectMonthDay = (day: MonthDayData) => {
  if (!day.isCurrentMonth) return
  emit('update:selectedDate', day.iso)
}

defineExpose({
  refresh: fetchCounts,
})
</script>

<template>
  <div class="bg-surface border border-border rounded-xl p-5">
    <!-- Onglets + navigation -->
    <div v-if="!isSuperEmploye" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-5">
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
        <button
          class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-foreground hover:bg-border/50 transition-colors"
          @click="goPrev"
        >
          <ChevronLeft class="w-4 h-4" />
        </button>
        <span class="bg-background border border-border rounded-lg px-3 py-1.5 text-foreground text-sm font-medium">
          {{ periodLabel }}
        </span>
        <button
          class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-foreground hover:bg-border/50 transition-colors"
          @click="goNext"
        >
          <ChevronRight class="w-4 h-4" />
        </button>
      </div>
    </div>

    <div v-else class="mb-5">
      <span class="bg-background border border-border rounded-lg px-3 py-1.5 text-foreground text-sm font-medium">
        {{ periodLabel }}
      </span>
    </div>

    <div v-if="isLoading" class="grid grid-cols-7 gap-2 md:gap-4">
      <div v-for="i in 7" :key="i" class="flex flex-col items-center gap-1.5 p-2 md:p-3">
        <Skeleton class="h-3 w-6" />
        <Skeleton class="w-8 h-8 rounded-full" />
        <Skeleton class="h-2.5 w-8" />
      </div>
    </div>
    <p v-else-if="error" class="text-red-400 text-xs">
      {{ error }}
    </p>

    <template v-else>
      <!-- Vue semaine -->
      <div v-if="activeView === 'week'" class="grid grid-cols-7 gap-2 md:gap-4">
        <button
          v-for="day in weekDays"
          :key="day.iso"
          class="relative flex flex-col items-center text-center rounded-lg p-2 md:p-3 transition-colors min-w-0"
          :class="[
            day.isSelected
              ? 'bg-primary/10 border border-primary/30 z-10 -mx-2 md:-mx-3'
              : 'border border-transparent hover:bg-border/30'
          ]"
          @click="selectWeekDay(day)"
        >
          <span class="text-muted text-xs font-medium mb-1">{{ day.dayName }}</span>
          <span
            class="w-8 h-8 flex items-center justify-center rounded-full text-sm font-bold mb-1.5"
            :class="day.isSelected ? 'bg-primary text-white' : 'text-foreground'"
          >
            {{ day.dayNumber }}
          </span>

          <div class="mb-2 leading-tight">
            <p class="text-foreground text-sm font-bold">{{ day.count }}</p>
            <p class="text-muted text-[10px] whitespace-nowrap">
              réservation{{ day.count !== 1 ? 's' : '' }}
            </p>
          </div>

          <div class="flex items-end h-8 w-full justify-center">
            <div
              class="w-2 rounded-sm transition-all"
              :class="countColorClass(day.count)"
              :style="{ height: `${Math.max((day.count / maxWeekCount) * 100, 8)}%` }"
            />
          </div>
        </button>
      </div>

      <!-- Vue mois -->
      <div v-else>
        <div class="grid grid-cols-7 mb-2">
          <span v-for="day in weekDayNames" :key="day" class="text-muted text-xs font-medium text-center">
            {{ day }}
          </span>
        </div>

        <div class="grid grid-cols-7 gap-y-1">
          <button
            v-for="day in monthDays"
            :key="day.iso"
            class="relative w-9 h-9 mx-auto flex items-center justify-center rounded-lg text-sm transition-colors"
            :class="[
              day.isCurrentMonth ? 'text-foreground hover:bg-border/50' : 'text-muted/40 cursor-default',
              day.isSelected ? 'bg-primary text-white hover:bg-primary' : '',
            ]"
            @click="selectMonthDay(day)"
          >
            {{ day.dayNumber }}
            <span
              v-if="day.count !== null && !day.isSelected"
              class="absolute bottom-1 w-1.5 h-1.5 rounded-full"
              :class="countColorClass(day.count)"
            />
          </button>
        </div>
      </div>
    </template>

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
