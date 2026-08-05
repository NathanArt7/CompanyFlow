<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  selectedDate: string
}>()

const emit = defineEmits<{
  'update:selectedDate': [string]
}>()

function toIso(date: Date): string {
  return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`
}

const dayLabels = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim']

const weekDays = computed(() => {
  const [y, m, d] = props.selectedDate.split('-').map(Number)
  const selected = new Date(y!, m! - 1, d!)
  // Lundi = début de semaine (getDay(): 0 = dimanche)
  const offsetToMonday = (selected.getDay() + 6) % 7
  const monday = new Date(selected)
  monday.setDate(selected.getDate() - offsetToMonday)

  return Array.from({ length: 7 }, (_, i) => {
    const date = new Date(monday)
    date.setDate(monday.getDate() + i)
    const iso = toIso(date)
    return {
      iso,
      label: `${dayLabels[i]} ${String(date.getDate()).padStart(2, '0')}/${String(date.getMonth() + 1).padStart(2, '0')}`,
      isSelected: iso === props.selectedDate,
    }
  })
})
</script>

<template>
  <div class="bg-surface border border-border rounded-xl overflow-hidden">
    <div class="grid grid-cols-7">
      <button
        v-for="day in weekDays"
        :key="day.iso"
        class="py-3 text-center text-sm font-medium transition-colors border-r border-border last:border-r-0"
        :class="day.isSelected
          ? 'bg-primary/15 text-primary-light'
          : 'text-muted hover:bg-background/40 hover:text-foreground'"
        @click="emit('update:selectedDate', day.iso)"
      >
        {{ day.label }}
      </button>
    </div>
  </div>
</template>
