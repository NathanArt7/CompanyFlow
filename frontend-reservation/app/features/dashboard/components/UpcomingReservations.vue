<script setup lang="ts">
import { ChevronDown } from 'lucide-vue-next'
import { ref, computed } from 'vue'
import type { UpcomingReservation } from '../types'

const reservations: UpcomingReservation[] = [
  {
    id: '1',
    time: '09:00 - 10:30',
    room: 'Salle de réunion A',
    location: 'Bâtiment A, 2ᵉ étage',
    event: "Réunion d'équipe",
    organizer: { name: 'Marc Lorem', initials: 'ML' },
    status: 'confirmed',
  },
  {
    id: '2',
    time: '11:00 - 12:30',
    room: 'Salle de réunion B',
    location: 'Bâtiment A, 2ᵉ étage',
    event: 'Présentation produit',
    organizer: { name: 'Sarah Johnson', initials: 'SJ' },
    status: 'confirmed',
  },
  {
    id: '3',
    time: '14:00 - 15:30',
    room: 'Salle de conférence',
    location: 'Bâtiment B, 1ᵉʳ étage',
    event: 'Point mensuel',
    organizer: { name: 'David Tchalla', initials: 'DT' },
    status: 'confirmed',
  },
  {
    id: '4',
    time: '16:00 - 17:00',
    room: 'Salle de réunion C',
    location: 'Bâtiment A, 3ᵉ étage',
    event: 'Entretien RH',
    organizer: { name: 'Amina Mohamed', initials: 'AM' },
    status: 'pending',
  },
  {
    id: '5',
    time: '17:30 - 18:30',
    room: 'Salle de réunion A',
    location: 'Bâtiment A, 2ᵉ étage',
    event: 'Brainstorming',
    organizer: { name: 'Yann Legrand', initials: 'YL' },
    status: 'confirmed',
  },
]

const visibleCount = ref(5)
const visibleReservations = computed(() => reservations.slice(0, visibleCount.value))
const hasMore = computed(() => visibleCount.value < reservations.length)

const showMore = () => {
  visibleCount.value += 5
}
</script>

<template>
  <div class="bg-surface border border-border rounded-xl p-5">
    <div class="flex items-center justify-between mb-5">
      <h2 class="text-foreground font-semibold">Réservations à venir</h2>
      <NuxtLink
        to="/reservations"
        class="text-xs bg-background border border-border rounded-lg px-3 py-1.5 text-foreground hover:border-primary/40 transition-colors whitespace-nowrap"
      >
        Voir toutes les réservations
      </NuxtLink>
    </div>

    <!-- Tableau desktop -->
    <div class="hidden md:block overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="text-left text-muted text-xs border-b border-border">
            <th class="font-medium pb-3 pr-4">Heure</th>
            <th class="font-medium pb-3 pr-4">Salle</th>
            <th class="font-medium pb-3 pr-4">Événement</th>
            <th class="font-medium pb-3 pr-4">Organisateur</th>
            <th class="font-medium pb-3">Statut</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="reservation in visibleReservations"
            :key="reservation.id"
            class="border-b border-border last:border-0"
          >
            <td class="py-4 pr-4 text-foreground whitespace-nowrap">{{ reservation.time }}</td>
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
            <td class="py-4">
              <ReservationStatusBadge :status="reservation.status" />
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Cards mobile -->
    <div class="md:hidden space-y-3">
      <div
        v-for="reservation in visibleReservations"
        :key="reservation.id"
        class="border border-border rounded-lg p-4"
      >
        <div class="flex items-center justify-between mb-2">
          <span class="text-foreground text-sm font-medium">{{ reservation.time }}</span>
          <ReservationStatusBadge :status="reservation.status" />
        </div>
        <p class="text-foreground font-medium">{{ reservation.room }}</p>
        <p class="text-muted text-xs mb-2">{{ reservation.location }}</p>
        <p class="text-foreground text-sm mb-3">{{ reservation.event }}</p>
        <div class="flex items-center gap-2">
          <div class="w-6 h-6 rounded-full bg-primary/20 flex items-center justify-center shrink-0">
            <span class="text-primary-light text-xs font-semibold">{{ reservation.organizer.initials }}</span>
          </div>
          <span class="text-muted text-xs">{{ reservation.organizer.name }}</span>
        </div>
      </div>
    </div>

    <!-- Afficher plus -->
    <button
      v-if="hasMore"
      class="w-full flex items-center justify-center gap-1.5 text-muted text-sm hover:text-foreground transition-colors mt-4 pt-4 border-t border-border"
      @click="showMore"
    >
      Afficher plus
      <ChevronDown class="w-4 h-4" />
    </button>
  </div>
</template>