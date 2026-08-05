<script setup lang="ts">
import { ChevronLeft, ChevronRight, Calendar } from 'lucide-vue-next'
import { computed } from 'vue'

const props = defineProps<{
  selectedDate: string
}>()

const emit = defineEmits<{
  'update:selectedDate': [string]
}>()

function todayIso(): string {
  const d = new Date()
  return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`
}

function toIso(date: Date): string {
  return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`
}

function parseIso(iso: string): Date {
  const [y, m, d] = iso.split('-').map(Number)
  return new Date(y!, m! - 1, d!)
}

function shiftWeek(offset: number) {
  const date = parseIso(props.selectedDate)
  date.setDate(date.getDate() + offset * 7)
  emit('update:selectedDate', toIso(date))
}

const weekLabel = computed(() => {
  const selected = parseIso(props.selectedDate)
  const offsetToMonday = (selected.getDay() + 6) % 7
  const monday = new Date(selected)
  monday.setDate(selected.getDate() - offsetToMonday)
  const sunday = new Date(monday)
  sunday.setDate(monday.getDate() + 6)

  const sameMonth = monday.getMonth() === sunday.getMonth() && monday.getFullYear() === sunday.getFullYear()

  const startLabel = monday.toLocaleDateString('fr-FR', sameMonth ? { day: 'numeric' } : { day: 'numeric', month: 'long' })
  const endLabel = sunday.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' })

  return `${startLabel} – ${endLabel}`
})
</script>

<template>
  <div class="bg-surface border border-border rounded-xl p-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
    <div class="flex items-center gap-3 flex-wrap">
      <button
        class="w-9 h-9 flex items-center justify-center rounded-lg border border-border text-muted hover:text-foreground hover:bg-border/50 transition-colors"
        @click="shiftWeek(-1)"
      >
        <ChevronLeft class="w-4 h-4" />
      </button>
      <button
        class="bg-background border border-border rounded-lg px-4 py-2 text-sm font-medium text-foreground hover:border-primary/40 transition-colors"
        @click="emit('update:selectedDate', todayIso())"
      >
        Cette semaine
      </button>
      <button
        class="w-9 h-9 flex items-center justify-center rounded-lg border border-border text-muted hover:text-foreground hover:bg-border/50 transition-colors"
        @click="shiftWeek(1)"
      >
        <ChevronRight class="w-4 h-4" />
      </button>

      <div class="flex items-center gap-2 text-foreground text-lg font-semibold ml-2">
        {{ weekLabel }}
        <Calendar class="w-4 h-4 text-muted" />
      </div>
    </div>
  </div>
</template>
