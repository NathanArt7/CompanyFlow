<script setup lang="ts">
import { ChevronDown } from 'lucide-vue-next'
import { ref, computed, onMounted } from 'vue'
import type { RawReservation, UpcomingReservation } from '../type'
import { useDashboardService } from '../services/dashboard.service'

const dashboardService = useDashboardService()
const authStore = useAuthStore()

// Ses réservations ne concernent que lui-même (scopées côté backend) : la colonne
// Organisateur n'a pas de sens pour le Super Employé.
const hideOrganizer = computed(() => authStore.user?.role === 'Super_Employe')
const isLoading = ref(true)
const error = ref<string | null>(null)
const reservations = ref<UpcomingReservation[]>([])

function mapReservation(r: RawReservation): UpcomingReservation {
  const prenom = r.user?.prenom ?? ''
  const nom = r.user?.nom ?? ''

  return {
    id: String(r.id),
    time: `${r.heure_debut.slice(0, 5)} - ${r.heure_fin.slice(0, 5)}`,
    room: r.room?.nom ?? '—',
    location: r.room?.localisation ?? '',
    event: r.motif,
    organizer: {
      name: `${prenom} ${nom}`.trim(),
      initials: `${prenom.charAt(0)}${nom.charAt(0)}`.toUpperCase(),
    },
    status: r.statut === 'CONFIRMEE' ? 'confirmed' : 'cancelled',
  }
}

const visibleCount = ref(5)
const visibleReservations = computed(() => reservations.value.slice(0, visibleCount.value))
const hasMore = computed(() => visibleCount.value < reservations.value.length)

const showMore = () => {
  visibleCount.value += 5
}

onMounted(async () => {
  try {
    const data = await dashboardService.getUpcomingReservations()
    reservations.value = data.map(mapReservation)
  } catch {
    error.value = 'Impossible de charger les réservations.'
  } finally {
    isLoading.value = false
  }
})
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

    <SkeletonTable v-if="isLoading" :rows="5" :columns="hideOrganizer ? 4 : 5" />
    <p v-else-if="error" class="text-red-400 text-xs">
      {{ error }}
    </p>
    <p v-else-if="reservations.length === 0" class="text-muted text-xs">
      Aucune réservation à venir.
    </p>

    <template v-else>
    <!-- Tableau desktop -->
    <div class="hidden md:block overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="text-left text-muted text-xs border-b border-border">
            <th class="font-medium pb-3 pr-4">Heure</th>
            <th class="font-medium pb-3 pr-4">Salle</th>
            <th class="font-medium pb-3 pr-4">Événement</th>
            <th v-if="!hideOrganizer" class="font-medium pb-3 pr-4">Organisateur</th>
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
            <td v-if="!hideOrganizer" class="py-4 pr-4">
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-primary/20 flex items-center justify-center shrink-0">
                  <span class="text-primary-light text-xs font-semibold">{{ reservation.organizer.initials }}</span>
                </div>
                <span class="text-foreground whitespace-nowrap">{{ reservation.organizer.name }}</span>
              </div>
            </td>
            <td class="py-4">
              <DashboardReservationStatusBadge :status="reservation.status" />
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
          <DashboardReservationStatusBadge :status="reservation.status" />
        </div>
        <p class="text-foreground font-medium">{{ reservation.room }}</p>
        <p class="text-muted text-xs mb-2">{{ reservation.location }}</p>
        <p class="text-foreground text-sm mb-3">{{ reservation.event }}</p>
        <div v-if="!hideOrganizer" class="flex items-center gap-2">
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
    </template>
  </div>
</template>