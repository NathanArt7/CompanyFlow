<script setup lang="ts">
import { MoreHorizontal, ChevronLeft, ChevronRight, ChevronDown, Presentation, Monitor } from 'lucide-vue-next'
import { ref } from 'vue'
import type { ReservationRow } from '../types'

const reservations: ReservationRow[] = [
  {
    id: '1', dotColor: 'purple', time: '09:00 - 10:30', room: 'Salle de réunion A', location: 'Bâtiment A, 2ᵉ étage',
    event: 'Réunion Marketing', organizer: { name: 'Marc Lorem', initials: 'ML' }, status: 'confirmed',
    equipmentCount: 2, extraEquipmentCount: 2,
  },
  {
    id: '2', dotColor: 'orange', time: '10:30 - 12:00', room: 'Salle de réunion B', location: 'Bâtiment A, 2ᵉ étage',
    event: 'Point RH', organizer: { name: 'Sarah Johnson', initials: 'SJ' }, status: 'confirmed',
    equipmentCount: 2, extraEquipmentCount: 1,
  },
  {
    id: '3', dotColor: 'purple', time: '13:00 - 14:30', room: 'Salle de conférence', location: 'Bâtiment B, 1ᵉʳ étage',
    event: 'Formation équipe', organizer: { name: 'David Tchalla', initials: 'DT' }, status: 'confirmed',
    equipmentCount: 2,
  },
  {
    id: '4', dotColor: 'purple', time: '15:00 - 16:30', room: 'Salle de réunion C', location: 'Bâtiment A, 3ᵉ étage',
    event: 'Sprint Review', organizer: { name: 'Amina Mohamed', initials: 'AM' }, status: 'confirmed',
    equipmentCount: 1,
  },
  {
    id: '5', dotColor: 'purple', time: '17:00 - 18:00', room: 'Salle de réunion A', location: 'Bâtiment A, 2ᵉ étage',
    event: 'Entretien individuel', organizer: { name: 'Yann Legrand', initials: 'YL' }, status: 'confirmed',
    equipmentCount: 0,
  },
  {
    id: '6', dotColor: 'red', time: '19:00 - 20:00', room: 'Salle de réunion B', location: 'Bâtiment A, 2ᵉ étage',
    event: 'Session client', organizer: { name: 'Kevin Bernard', initials: 'KB' }, status: 'cancelled',
    equipmentCount: 0,
  },
]

const dotColorClass: Record<string, string> = {
  purple: 'bg-primary',
  orange: 'bg-orange-500',
  red: 'bg-red-500',
}

const currentPage = ref(1)
const totalPages = 2
const totalResults = 12
const pageSize = ref('10')
</script>

<template>
  <div class="bg-surface border border-border rounded-xl overflow-hidden">
    <!-- Tableau desktop -->
    <div class="hidden md:block overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="text-left text-muted text-xs border-b border-border">
            <th class="font-medium py-3 pl-5 pr-4">Heure</th>
            <th class="font-medium py-3 pr-4">Salle</th>
            <th class="font-medium py-3 pr-4">Événement</th>
            <th class="font-medium py-3 pr-4">Organisateur</th>
            <th class="font-medium py-3 pr-4">Statut</th>
            <th class="font-medium py-3 pr-4">Équipements</th>
            <th class="font-medium py-3 pr-5">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="reservation in reservations"
            :key="reservation.id"
            class="border-b border-border last:border-0 hover:bg-background/40 transition-colors"
          >
            <td class="py-4 pl-5 pr-4 whitespace-nowrap">
              <div class="flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full shrink-0" :class="dotColorClass[reservation.dotColor]" />
                <span class="text-foreground">{{ reservation.time }}</span>
              </div>
            </td>
            <td class="py-4 pr-4">
              <p class="text-foreground font-medium">{{ reservation.room }}</p>
              <p class="text-muted text-xs">{{ reservation.location }}</p>
            </td>
            <td class="py-4 pr-4 text-foreground">{{ reservation.event }}</td>
            <td class="py-4 pr-4">
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-primary/20 flex items-center justify-center shrink-0">
                  <span class="text-primary-light text-xs font-semibold">{{ reservation.organizer.initials }}</span>
                </div>
                <span class="text-foreground whitespace-nowrap">{{ reservation.organizer.name }}</span>
              </div>
            </td>
            <td class="py-4 pr-4">
              <ReservationStatusBadge :status="reservation.status" />
            </td>
            <td class="py-4 pr-4">
              <div v-if="reservation.equipmentCount > 0" class="flex items-center gap-1">
                <span class="w-6 h-6 rounded-md bg-background border border-border flex items-center justify-center">
                  <Presentation class="w-3.5 h-3.5 text-muted" />
                </span>
                <span v-if="reservation.equipmentCount > 1" class="w-6 h-6 rounded-md bg-background border border-border flex items-center justify-center">
                  <Monitor class="w-3.5 h-3.5 text-muted" />
                </span>
                <span v-if="reservation.extraEquipmentCount" class="text-muted text-xs ml-1">
                  +{{ reservation.extraEquipmentCount }}
                </span>
              </div>
              <span v-else class="text-muted">—</span>
            </td>
            <td class="py-4 pr-5">
              <button class="w-8 h-8 flex items-center justify-center rounded-lg text-muted hover:text-foreground hover:bg-border/50 transition-colors">
                <MoreHorizontal class="w-4 h-4" />
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Cards mobile -->
    <div class="md:hidden divide-y divide-border">
      <div v-for="reservation in reservations" :key="reservation.id" class="p-4">
        <div class="flex items-center justify-between mb-2">
          <div class="flex items-center gap-2">
            <span class="w-1.5 h-1.5 rounded-full shrink-0" :class="dotColorClass[reservation.dotColor]" />
            <span class="text-foreground text-sm font-medium">{{ reservation.time }}</span>
          </div>
          <ReservationStatusBadge :status="reservation.status" />
        </div>
        <p class="text-foreground font-medium">{{ reservation.room }}</p>
        <p class="text-muted text-xs mb-2">{{ reservation.location }}</p>
        <p class="text-foreground text-sm mb-3">{{ reservation.event }}</p>
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded-full bg-primary/20 flex items-center justify-center shrink-0">
              <span class="text-primary-light text-xs font-semibold">{{ reservation.organizer.initials }}</span>
            </div>
            <span class="text-muted text-xs">{{ reservation.organizer.name }}</span>
          </div>
          <button class="w-8 h-8 flex items-center justify-center rounded-lg text-muted hover:text-foreground hover:bg-border/50 transition-colors">
            <MoreHorizontal class="w-4 h-4" />
          </button>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-t border-border">
      <p class="text-muted text-xs">
        Affichage de 1 à 6 sur {{ totalResults }} réservations
      </p>

      <div class="flex items-center gap-2">
        <button
          class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-foreground hover:bg-border/50 transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
          :disabled="currentPage === 1"
          @click="currentPage--"
        >
          <ChevronLeft class="w-4 h-4" />
        </button>

        <button
          v-for="page in totalPages"
          :key="page"
          class="w-8 h-8 flex items-center justify-center rounded-lg text-sm font-medium transition-colors"
          :class="currentPage === page ? 'bg-primary text-white' : 'text-muted hover:text-foreground hover:bg-border/50'"
          @click="currentPage = page"
        >
          {{ page }}
        </button>

        <button
          class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-foreground hover:bg-border/50 transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
          :disabled="currentPage === totalPages"
          @click="currentPage++"
        >
          <ChevronRight class="w-4 h-4" />
        </button>

        <select
          v-model="pageSize"
          class="bg-background border border-border rounded-lg px-3 py-1.5 text-sm text-foreground focus:outline-none focus:border-primary transition-colors ml-1"
        >
          <option value="10">10 par page</option>
          <option value="20">20 par page</option>
          <option value="50">50 par page</option>
        </select>
      </div>
    </div>
  </div>
</template>