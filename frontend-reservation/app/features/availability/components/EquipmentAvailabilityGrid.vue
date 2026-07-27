<script setup lang="ts">
import { Monitor, Laptop, Mic, Speaker, Printer, MonitorPlay } from 'lucide-vue-next'
import { ref, computed } from 'vue'
import type { WeekDay, EquipmentAvailability } from '../types'

const timeSlots = ['08:00', '10:00', '12:00', '14:00', '16:00', '18:00', '18:00', '10:00', '12:00', '14:00', '16:00', '16:00', '18:00']

const weekDays: WeekDay[] = [
  { date: 19, dayLabel: 'Lun 19/05', isSelected: false },
  { date: 20, dayLabel: 'Mar 20/05', isSelected: false },
  { date: 21, dayLabel: 'Mer 21/05', isSelected: true },
  { date: 22, dayLabel: 'Jeu 22/05', isSelected: false },
  { date: 23, dayLabel: 'Ven 23/05', isSelected: false },
  { date: 24, dayLabel: 'Sam 24/05', isSelected: false },
  { date: 25, dayLabel: 'Dim 25/05', isSelected: false },
]

const selectedDate = ref(21)

// Données d'exemple par jour — à remplacer par un appel à ton service equipments/availability
const equipmentsByDate: Record<number, EquipmentAvailability[]> = {
  21: [
    {
      id: 'proj', name: 'Vidéoprojecteur Epson EB-X49', subtitle: 'Vidéoprojecteur', icon: MonitorPlay,
      events: [
        { startCol: 1, span: 2, status: 'reserved', title: "Réunion d'équipe", time: '09:00 - 11:00' },
        { startCol: 4, span: 2, status: 'reserved', title: 'Présentation client', time: '13:00 - 15:00' },
      ],
    },
    {
      id: 'laptop', name: 'Ordinateur portable Dell XPS 15', subtitle: 'Ordinateur portable', icon: Laptop,
      events: [
        { startCol: 2, span: 2, status: 'reserved', title: 'Entretien RH', time: '10:00 - 11:30' },
        { startCol: 6, span: 2, status: 'reserved', title: 'Réunion projet', time: '14:00 - 16:00' },
      ],
    },
    {
      id: 'mic', name: 'Microphone Shure SM58', subtitle: 'Microphone', icon: Mic,
      events: [
        { startCol: 4, span: 3, status: 'reserved', title: 'Conférence annuelle', time: '11:00 - 14:00' },
      ],
    },
    {
      id: 'speaker', name: 'Enceinte Bluetooth JBL Flip 6', subtitle: 'Enceinte', icon: Speaker,
      events: [
        { startCol: 0, span: 2, status: 'maintenance', title: 'Maintenance', time: '08:00 - 10:00' },
      ],
    },
    {
      id: 'printer', name: 'Imprimante HP LaserJet Pro', subtitle: 'Imprimante', icon: Printer,
      events: [
        { startCol: 1, span: 3, status: 'reserved', title: 'Formation interne', time: '09:00 - 12:00' },
        { startCol: 6, span: 2, status: 'reserved', title: 'Atelier pratique', time: '15:00 - 17:00' },
      ],
    },
    {
      id: 'screen', name: 'Écran interactif Samsung 75"', subtitle: 'Écran interactif', icon: Monitor,
      events: [
        { startCol: 0, span: 10, status: 'out_of_service', title: 'Hors service', time: '08:00 - 18:00' },
      ],
    },
  ],
}

const currentEquipments = computed(() => equipmentsByDate[selectedDate.value] ?? [])

const selectDay = (date: number) => {
  selectedDate.value = date
}

const statusBg: Record<string, string> = {
  available: 'bg-green-600/80',
  reserved: 'bg-blue-600',
  maintenance: 'bg-orange-600',
  out_of_service: 'bg-red-900/60',
}
</script>

<template>
  <div>
    <h2 class="text-foreground text-xl font-bold mb-3">Disponibilité des équipements</h2>

    <AvailabilityLegend class="mb-4" />

    <div class="bg-surface border border-border rounded-xl overflow-hidden">
      <!-- Bande des jours -->
      <div class="grid grid-cols-7 border-b border-border">
        <button
          v-for="day in weekDays"
          :key="day.date"
          class="py-3 text-center text-sm font-medium transition-colors border-r border-border last:border-r-0"
          :class="selectedDate === day.date
            ? 'bg-primary/15 text-primary-light'
            : 'text-muted hover:bg-background/40 hover:text-foreground'"
          @click="selectDay(day.date)"
        >
          {{ day.dayLabel }}
        </button>
      </div>

      <div class="overflow-x-auto">
        <div class="min-w-[900px]">
          <!-- En-tête : colonne "Équipements" + créneaux horaires -->
          <div class="grid grid-cols-[240px_1fr] border-b border-border">
            <div class="px-4 py-3 text-foreground text-sm font-semibold">Équipements</div>
            <div class="grid" :style="{ gridTemplateColumns: `repeat(${timeSlots.length}, 1fr)` }">
              <div
                v-for="(slot, index) in timeSlots"
                :key="index"
                class="px-2 py-3 text-center text-muted text-xs font-medium border-l border-border"
              >
                {{ slot }}
              </div>
            </div>
          </div>

          <!-- Lignes des équipements -->
          <div
            v-for="equipment in currentEquipments"
            :key="equipment.id"
            class="grid grid-cols-[240px_1fr] border-b border-border last:border-0"
          >
            <div class="flex items-center gap-2.5 px-4 py-3">
              <component :is="equipment.icon" class="w-4 h-4 text-muted shrink-0" />
              <div class="min-w-0">
                <p class="text-foreground text-sm font-medium truncate">{{ equipment.name }}</p>
                <p class="text-muted text-xs truncate">{{ equipment.subtitle }}</p>
              </div>
            </div>

            <div class="relative grid" :style="{ gridTemplateColumns: `repeat(${timeSlots.length}, 1fr)` }">
              <div
                v-for="(slot, index) in timeSlots"
                :key="index"
                class="h-16 border-l border-border bg-green-600/20"
              />

              <button
                v-for="(event, index) in equipment.events"
                :key="index"
                class="absolute top-0 h-16 flex flex-col items-center justify-center px-2 text-center transition-opacity hover:opacity-90"
                :class="statusBg[event.status]"
                :style="{
                  left: `${(event.startCol / timeSlots.length) * 100}%`,
                  width: `${(event.span / timeSlots.length) * 100}%`,
                }"
              >
                <span class="text-white text-xs font-medium truncate w-full">{{ event.title }}</span>
                <span v-if="event.time" class="text-white/70 text-[10px]">{{ event.time }}</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="flex items-center justify-center gap-2 py-3 border-t border-border">
        <span class="text-muted text-xs">
          ⓘ Cliquez sur un créneau réservé pour voir les détails de la réservation.
        </span>
      </div>
    </div>
  </div>
</template>