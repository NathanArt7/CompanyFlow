<script setup lang="ts">
import { ChevronLeft, ChevronRight, Info, Pencil, Ban } from 'lucide-vue-next'
import { computed } from 'vue'
import type { RawReservation, ReservationRow, ReservationStatus } from '../type'
import { useAuthStore } from '~/stores/auth.store'

const props = defineProps<{
  reservations: RawReservation[]
  isLoading: boolean
  error: string | null
  page: number
  perPage: number
  total: number
  lastPage: number
  hidePagination?: boolean
  hideOrganizer?: boolean
}>()

const emit = defineEmits<{
  'update:page': [number]
  'update:perPage': [number]
  details: [RawReservation]
  edit: [RawReservation]
  cancel: [RawReservation]
}>()

const authStore = useAuthStore()

function deriveStatus(r: RawReservation): ReservationStatus {
  if (r.statut === 'ANNULEE') return 'cancelled'

  const datePart = r.date_reservation.slice(0, 10)
  const start = new Date(`${datePart}T${r.heure_debut}`)
  const end = new Date(`${datePart}T${r.heure_fin}`)
  const now = new Date()

  if (now < start) return 'confirmed'
  if (now < end) return 'ongoing'
  return 'past'
}

function mapReservation(r: RawReservation): ReservationRow & { raw: RawReservation } {
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
    status: deriveStatus(r),
    equipmentCount: r.equipments.length,
    raw: r,
  }
}

const rows = computed(() => props.reservations.map(mapReservation))

function isOwner(reservation: RawReservation) {
  return reservation.user_id === authStore.user?.id
}

function canCancel(reservation: RawReservation, status: ReservationStatus) {
  if (reservation.statut === 'ANNULEE' || status === 'past') return false
  return isOwner(reservation) || authStore.permissions.includes('annuler_reservation')
}

function canEdit(reservation: RawReservation, status: ReservationStatus) {
  if (reservation.statut === 'ANNULEE' || status === 'past') return false
  return isOwner(reservation)
}

const pageSize = computed({
  get: () => String(props.perPage),
  set: (value: string) => emit('update:perPage', Number(value)),
})

const rangeStart = computed(() => props.total === 0 ? 0 : (props.page - 1) * props.perPage + 1)
const rangeEnd = computed(() => Math.min(props.page * props.perPage, props.total))
</script>

<template>
  <div class="bg-surface border border-border rounded-xl overflow-hidden">
    <SkeletonTable v-if="isLoading" :rows="perPage > 8 ? 8 : perPage" :columns="hideOrganizer ? 6 : 7" />
    <p v-else-if="error" class="text-red-400 text-xs p-5">
      {{ error }}
    </p>
    <p v-else-if="rows.length === 0" class="text-muted text-xs p-5">
      Aucune réservation pour cette journée.
    </p>

    <template v-else>
      <!-- Tableau desktop -->
      <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="text-left text-muted text-xs border-b border-border">
              <th class="font-medium py-3 pl-5 pr-4">Heure</th>
              <th class="font-medium py-3 pr-4">Salle</th>
              <th class="font-medium py-3 pr-4">Événement</th>
              <th v-if="!hideOrganizer" class="font-medium py-3 pr-4">Organisateur</th>
              <th class="font-medium py-3 pr-4">Statut</th>
              <th class="font-medium py-3 pr-4">Équipements</th>
              <th class="font-medium py-3 pr-5">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="reservation in rows"
              :key="reservation.id"
              class="border-b border-border last:border-0 hover:bg-background/40 transition-colors"
            >
              <td class="py-4 pl-5 pr-4 whitespace-nowrap text-foreground">{{ reservation.time }}</td>
              <td class="py-4 pr-4">
                <p class="font-medium" :class="reservation.raw.room?.deleted_at ? 'text-red-400' : 'text-foreground'">{{ reservation.room }}</p>
                <p v-if="reservation.raw.room?.deleted_at" class="text-red-400 text-xs">(Salle supprimée)</p>
                <p v-else class="text-muted text-xs">{{ reservation.location }}</p>
              </td>
              <td class="py-4 pr-4 text-foreground">{{ reservation.event }}</td>
              <td v-if="!hideOrganizer" class="py-4 pr-4">
                <div class="flex items-center gap-2">
                  <div class="w-7 h-7 rounded-full bg-primary/20 flex items-center justify-center shrink-0">
                    <span class="text-primary-light text-xs font-semibold">{{ reservation.organizer.initials }}</span>
                  </div>
                  <div>
                    <p class="whitespace-nowrap" :class="reservation.raw.user?.deleted_at ? 'text-red-400' : 'text-foreground'">{{ reservation.organizer.name }}</p>
                    <p v-if="reservation.raw.user?.deleted_at" class="text-red-400 text-xs">(Utilisateur supprimé)</p>
                  </div>
                </div>
              </td>
              <td class="py-4 pr-4">
                <ReservationStatusBadge :status="reservation.status" />
              </td>
              <td class="py-4 pr-4 text-foreground">
                {{ reservation.equipmentCount > 0 ? reservation.equipmentCount : '—' }}
              </td>
              <td class="py-4 pr-5 relative">
                <ActionsMenu v-slot="{ close }">
                  <button
                    class="w-full flex items-center gap-2 px-3 py-2.5 text-sm text-foreground hover:bg-border/50 transition-colors"
                    @click="emit('details', reservation.raw); close()"
                  >
                    <Info class="w-3.5 h-3.5" />
                    Détails de la réservation
                  </button>
                  <button
                    v-if="canEdit(reservation.raw, reservation.status)"
                    class="w-full flex items-center gap-2 px-3 py-2.5 text-sm text-foreground hover:bg-border/50 transition-colors"
                    @click="emit('edit', reservation.raw); close()"
                  >
                    <Pencil class="w-3.5 h-3.5" />
                    Modifier
                  </button>
                  <button
                    v-if="canCancel(reservation.raw, reservation.status)"
                    class="w-full flex items-center gap-2 px-3 py-2.5 text-sm text-red-400 hover:bg-border/50 transition-colors"
                    @click="emit('cancel', reservation.raw); close()"
                  >
                    <Ban class="w-3.5 h-3.5" />
                    Annuler
                  </button>
                </ActionsMenu>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Cards mobile -->
      <div class="md:hidden divide-y divide-border">
        <div v-for="reservation in rows" :key="reservation.id" class="p-4">
          <div class="flex items-center justify-between mb-2">
            <span class="text-foreground text-sm font-medium">{{ reservation.time }}</span>
            <ReservationStatusBadge :status="reservation.status" />
          </div>
          <p class="font-medium" :class="reservation.raw.room?.deleted_at ? 'text-red-400' : 'text-foreground'">{{ reservation.room }}</p>
          <p v-if="reservation.raw.room?.deleted_at" class="text-red-400 text-xs mb-2">(Salle supprimée)</p>
          <p v-else class="text-muted text-xs mb-2">{{ reservation.location }}</p>
          <p class="text-foreground text-sm mb-3">{{ reservation.event }}</p>
          <div class="flex items-center justify-between">
            <div v-if="!hideOrganizer" class="flex items-center gap-2">
              <div class="w-6 h-6 rounded-full bg-primary/20 flex items-center justify-center shrink-0">
                <span class="text-primary-light text-xs font-semibold">{{ reservation.organizer.initials }}</span>
              </div>
              <div>
                <p class="text-xs" :class="reservation.raw.user?.deleted_at ? 'text-red-400' : 'text-muted'">{{ reservation.organizer.name }}</p>
                <p v-if="reservation.raw.user?.deleted_at" class="text-red-400 text-xs">(Utilisateur supprimé)</p>
              </div>
            </div>
            <div v-else />
            <ActionsMenu v-slot="{ close }">
              <button
                class="w-full flex items-center gap-2 px-3 py-2.5 text-sm text-foreground hover:bg-border/50 transition-colors"
                @click="emit('details', reservation.raw); close()"
              >
                <Info class="w-3.5 h-3.5" />
                Détails de la réservation
              </button>
              <button
                v-if="canEdit(reservation.raw, reservation.status)"
                class="w-full flex items-center gap-2 px-3 py-2.5 text-sm text-foreground hover:bg-border/50 transition-colors"
                @click="emit('edit', reservation.raw); close()"
              >
                <Pencil class="w-3.5 h-3.5" />
                Modifier
              </button>
              <button
                v-if="canCancel(reservation.raw, reservation.status)"
                class="w-full flex items-center gap-2 px-3 py-2.5 text-sm text-red-400 hover:bg-border/50 transition-colors"
                @click="emit('cancel', reservation.raw); close()"
              >
                <Ban class="w-3.5 h-3.5" />
                Annuler
              </button>
            </ActionsMenu>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="!hidePagination" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-t border-border">
        <p class="text-muted text-xs">
          Affichage de {{ rangeStart }} à {{ rangeEnd }} sur {{ total }} réservations
        </p>

        <div class="flex items-center gap-2">
          <button
            class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-foreground hover:bg-border/50 transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
            :disabled="page === 1"
            @click="emit('update:page', page - 1)"
          >
            <ChevronLeft class="w-4 h-4" />
          </button>

          <button
            v-for="p in lastPage"
            :key="p"
            class="w-8 h-8 flex items-center justify-center rounded-lg text-sm font-medium transition-colors"
            :class="page === p ? 'bg-primary text-white' : 'text-muted hover:text-foreground hover:bg-border/50'"
            @click="emit('update:page', p)"
          >
            {{ p }}
          </button>

          <button
            class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-foreground hover:bg-border/50 transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
            :disabled="page === lastPage"
            @click="emit('update:page', page + 1)"
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
    </template>
  </div>
</template>
