<script setup lang="ts">
import { Users, Monitor, Archive } from 'lucide-vue-next'
import { ref, computed } from 'vue'
import type { WeekDay, AvailabilityRoom, RoomEvent } from '../types'

const timeSlots = ['08:00', '09:00', '10:00', '12:00', '12:00', '14:00', '15:00', '16:00', '17:00', '18:00']

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

// Données d'exemple par jour — à remplacer par un appel à ton service availability
const roomsByDate: Record<number, AvailabilityRoom[]> = {
  21: [
    {
      id: 'a', name: 'Salle de réunion A', subtitle: 'Réunion • 12 places', icon: Users,
      events: [
        { startCol: 1, span: 2, status: 'reserved', title: "Réunion d'équipe", time: '09:00 - 11:00' },
        { startCol: 4, span: 2, status: 'reserved', title: 'Présentation client', time: '13:00 - 15:00' },
      ],
    },
    {
      id: 'b', name: 'Salle de réunion B', subtitle: 'Réunion • 8 places', icon: Users,
      events: [
        { startCol: 2, span: 2, status: 'reserved', title: 'Entretien RH', time: '10:00 - 11:30' },
        { startCol: 6, span: 2, status: 'reserved', title: 'Réunion projet', time: '14:00 - 16:00' },
      ],
    },
    {
      id: 'conf', name: 'Salle de conférence', subtitle: 'Conférence • 20 places', icon: Monitor,
      events: [
        { startCol: 0, span: 2, status: 'maintenance', title: 'Maintenance', time: '08:00 - 10:00' },
        { startCol: 4, span: 3, status: 'reserved', title: 'Conférence annuelle', time: '11:00 - 14:00' },
      ],
    },
    {
      id: 'formation', name: 'Salle de formation', subtitle: 'Formation • 15 places', icon: Users,
      events: [
        { startCol: 1, span: 3, status: 'reserved', title: 'Formation interne', time: '09:00 - 12:00' },
        { startCol: 6, span: 2, status: 'reserved', title: 'Atelier pratique', time: '15:00 - 17:00' },
      ],
    },
    {
      id: 'stock1', name: 'Salle de stockage 1', subtitle: 'Stockage • -', icon: Archive,
      events: [],
    },
    {
      id: 'stock2', name: 'Salle de stockage 2', subtitle: 'Stockage • -', icon: Archive,
      events: [
        { startCol: 0, span: 10, status: 'out_of_service', title: 'Hors service', time: '08:00 - 18:00' },
      ],
    },
  ],
  // Les autres jours (19, 20, 22, 23, 24, 25) auraient leurs propres tableaux ici
}

const currentRooms = computed(() => roomsByDate[selectedDate.value] ?? [])

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
        <!-- En-tête : colonne "Salles" + créneaux horaires -->
        <div class="grid grid-cols-[220px_1fr] border-b border-border">
          <div class="px-4 py-3 text-foreground text-sm font-semibold">Salles</div>
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

        <!-- Lignes des salles -->
        <div
          v-for="room in currentRooms"
          :key="room.id"
          class="grid grid-cols-[220px_1fr] border-b border-border last:border-0"
        >
          <div class="flex items-center gap-2.5 px-4 py-3">
            <component :is="room.icon" class="w-4 h-4 text-muted shrink-0" />
            <div class="min-w-0">
              <p class="text-foreground text-sm font-medium truncate">{{ room.name }}</p>
              <p class="text-muted text-xs truncate">{{ room.subtitle }}</p>
            </div>
          </div>

          <!-- Piste de créneaux -->
          <div
            class="relative grid"
            :style="{ gridTemplateColumns: `repeat(${timeSlots.length}, 1fr)` }"
          >
            <!-- Fond disponible par défaut sur toute la ligne -->
            <div
              v-for="(slot, index) in timeSlots"
              :key="index"
              class="h-16 border-l border-border bg-green-600/20"
            />

            <!-- Événements superposés -->
            <button
              v-for="(event, index) in room.events"
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

    <!-- Note d'aide -->
    <div class="flex items-center justify-center gap-2 py-3 border-t border-border">
      <span class="text-muted text-xs">
        ⓘ Cliquez sur un créneau réservé pour voir les détails de la réservation.
      </span>
    </div>
  </div>
</template>