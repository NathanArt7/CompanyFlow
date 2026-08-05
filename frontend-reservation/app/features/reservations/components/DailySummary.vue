<script setup lang="ts">
import { Calendar, Building2, CalendarX, Monitor } from 'lucide-vue-next'
import { ref, computed, onMounted, watch } from 'vue'
import type { SummaryCard } from '../type'
import { useReservationsService } from '../services/reservations.service'

const props = defineProps<{
  selectedDate: string
}>()

const reservationsService = useReservationsService()

const isLoading = ref(true)
const error = ref<string | null>(null)
const cards = ref<SummaryCard[]>([])

function parseIsoLocal(iso: string): Date {
  const [year, month, day] = iso.split('-').map(Number)
  return new Date(year, month - 1, day)
}

const formattedDate = computed(() => {
  const formatted = new Intl.DateTimeFormat('fr-FR', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  }).format(parseIsoLocal(props.selectedDate))
  return formatted.charAt(0).toUpperCase() + formatted.slice(1)
})

function pct(part: number, total: number): number {
  return total > 0 ? Math.round((part / total) * 100) : 0
}

async function load() {
  isLoading.value = true
  error.value = null
  try {
    const summary = await reservationsService.getDailySummary(props.selectedDate)
    const total = summary.reservations.total

    cards.value = [
      {
        icon: Calendar,
        iconBg: 'bg-primary',
        value: total,
        label: 'Réservations',
        barColor: 'bg-primary',
        percentage: 100,
        percentageLabel: '100% du total',
      },
      {
        icon: Building2,
        iconBg: 'bg-blue-500',
        value: summary.rooms_occupied,
        label: 'Salles occupées',
        barColor: 'bg-blue-500',
        percentage: pct(summary.rooms_occupied, total),
        percentageLabel: `${pct(summary.rooms_occupied, total)}% du total`,
      },
      {
        icon: CalendarX,
        iconBg: 'bg-red-500',
        value: summary.reservations.cancelled,
        label: 'Annulées',
        barColor: 'bg-red-500',
        percentage: pct(summary.reservations.cancelled, total),
        percentageLabel: `${pct(summary.reservations.cancelled, total)}% du total`,
      },
      {
        icon: Monitor,
        iconBg: 'bg-green-500',
        value: summary.equipments.reserved,
        label: 'Équipements réservés',
        barColor: 'bg-green-500',
        percentage: pct(summary.equipments.reserved, summary.equipments.total),
        percentageLabel: `${pct(summary.equipments.reserved, summary.equipments.total)}% de la capacité`,
      },
    ]
  } catch {
    error.value = 'Impossible de charger le résumé de la journée.'
    cards.value = []
  } finally {
    isLoading.value = false
  }
}

onMounted(load)
watch(() => props.selectedDate, load)

defineExpose({
  refresh: load,
})
</script>

<template>
  <div class="bg-surface border border-border rounded-xl p-5">
    <div class="flex items-center justify-between mb-5">
      <h2 class="text-foreground font-semibold">Résumé de la journée</h2>
      <span class="flex items-center gap-1.5 bg-background border border-border rounded-lg px-3 py-1.5 text-foreground text-xs">
        <Calendar class="w-3.5 h-3.5 text-muted" />
        {{ formattedDate }}
      </span>
    </div>

    <div v-if="isLoading" class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <div v-for="i in 4" :key="i">
        <Skeleton class="w-10 h-10 rounded-lg mb-3" />
        <Skeleton class="h-7 w-12 mb-2" />
        <Skeleton class="h-4 w-24 mb-3" />
        <Skeleton class="h-1 w-full rounded-full mb-1.5" />
        <Skeleton class="h-3 w-16" />
      </div>
    </div>
    <p v-else-if="error" class="text-red-400 text-xs">
      {{ error }}
    </p>
    <div v-else class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <div v-for="card in cards" :key="card.label">
        <div
          class="w-10 h-10 rounded-lg flex items-center justify-center mb-3"
          :class="card.iconBg"
        >
          <component :is="card.icon" class="w-5 h-5 text-white" />
        </div>

        <p class="text-foreground text-2xl md:text-3xl font-bold">{{ card.value }}</p>
        <p class="text-muted text-sm mt-0.5">{{ card.label }}</p>

        <div class="w-full h-1 bg-background rounded-full overflow-hidden mt-3 mb-1.5">
          <div
            class="h-full rounded-full"
            :class="card.barColor"
            :style="{ width: `${Math.min(card.percentage, 100)}%` }"
          />
        </div>
        <p class="text-muted text-xs">{{ card.percentageLabel }}</p>
      </div>
    </div>
  </div>
</template>
