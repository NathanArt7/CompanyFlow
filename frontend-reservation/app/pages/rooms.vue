<script setup lang="ts">
definePageMeta({ layout: 'default', middleware: 'auth' })

import { ref, watch, onMounted, computed } from 'vue'
import type { RawRoom, RoomFilters } from '~/features/rooms/type'
import { useRoomService } from '~/features/rooms/services/room.service'
import type { ExportColumn, ExportRow } from '~/composables/useTableExport'
import { useReservationsService } from '~/features/reservations/services/reservations.service'
import type { RawReservation } from '~/features/reservations/type'

const roomService = useRoomService()
const reservationsService = useReservationsService()

const filters = ref<RoomFilters>({ search: '', type: null, statut: null })
const page = ref(1)
const perPage = ref(10)

const rooms = ref<RawRoom[]>([])
const total = ref(0)
const lastPage = ref(1)
const isLoading = ref(false)
const error = ref<string | null>(null)

async function loadRooms() {
  isLoading.value = true
  error.value = null
  try {
    const response = await roomService.paginate({
      search: filters.value.search || undefined,
      type: filters.value.type ?? undefined,
      statut: filters.value.statut ?? undefined,
      page: page.value,
      per_page: perPage.value,
      sort: 'nom',
      direction: 'asc',
    })
    rooms.value = response.data
    total.value = response.total
    lastPage.value = response.last_page
  } catch {
    error.value = 'Impossible de charger les salles.'
    rooms.value = []
  } finally {
    isLoading.value = false
  }
}

watch(filters, () => {
  page.value = 1
  loadRooms()
}, { deep: true })
watch([page, perPage], loadRooms)
onMounted(loadRooms)

const exportColumns: ExportColumn[] = [
  { key: 'nom', label: 'Nom' },
  { key: 'code', label: 'Code' },
  { key: 'type', label: 'Type' },
  { key: 'capacite', label: 'Capacité' },
  { key: 'localisation', label: 'Localisation' },
  { key: 'statut', label: 'Statut' },
]

const exportRows = computed<ExportRow[]>(() => rooms.value.map(r => ({
  nom: r.nom,
  code: r.code,
  type: r.type.label,
  capacite: r.capacite ?? '—',
  localisation: r.localisation,
  statut: r.statut.label,
})))

function onFiltersChange(next: RoomFilters) {
  filters.value = next
}

const roomsStatsRef = ref<{ refresh: () => void } | null>(null)

function refreshAfterChange() {
  loadRooms()
  roomsStatsRef.value?.refresh()
}

// Création / édition
const isFormModalOpen = ref(false)
const editingRoom = ref<RawRoom | null>(null)

function openCreateModal() {
  editingRoom.value = null
  isFormModalOpen.value = true
}

function openEditModal(room: RawRoom) {
  editingRoom.value = room
  isFormModalOpen.value = true
}

function onRoomSaved() {
  isFormModalOpen.value = false
  page.value = 1
  refreshAfterChange()
}

// Suppression
const roomPendingDelete = ref<RawRoom | null>(null)
const upcomingReservationsForDelete = ref<RawReservation[]>([])
const isCheckingReservations = ref(false)
const isDeleting = ref(false)
const deleteError = ref<string | null>(null)

function todayIso(): string {
  const d = new Date()
  return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`
}

function formatReservationDate(reservation: RawReservation): string {
  const date = new Date(reservation.date_reservation).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' })
  return `${date} de ${reservation.heure_debut.slice(0, 5)} à ${reservation.heure_fin.slice(0, 5)}`
}

async function requestDelete(room: RawRoom) {
  roomPendingDelete.value = room
  deleteError.value = null
  upcomingReservationsForDelete.value = []

  isCheckingReservations.value = true
  try {
    const response = await reservationsService.getReservations({
      room_id: room.id,
      from_date: todayIso(),
      status: 'CONFIRMEE',
      per_page: 100,
    })
    upcomingReservationsForDelete.value = response.data
  } catch {
    // Échec silencieux : la suppression reste possible, seul l'aperçu des réservations
    // impactées ne s'affiche pas — le backend annule quoi qu'il arrive les réservations
    // concernées et prévient leurs organisateurs.
  } finally {
    isCheckingReservations.value = false
  }
}

async function confirmDelete() {
  if (!roomPendingDelete.value) return

  isDeleting.value = true
  deleteError.value = null
  try {
    await roomService.remove(roomPendingDelete.value.id)
    roomPendingDelete.value = null
    upcomingReservationsForDelete.value = []
    if (rooms.value.length === 1 && page.value > 1) page.value -= 1
    refreshAfterChange()
  } catch {
    deleteError.value = 'Impossible de supprimer cette salle.'
  } finally {
    isDeleting.value = false
  }
}
</script>

<template>
  <div class="space-y-6">
    <RoomsHeader
      :export-columns="exportColumns"
      :export-rows="exportRows"
      @create-click="openCreateModal"
    />
    <RoomsStats ref="roomsStatsRef" />

    <div class="bg-surface border border-border rounded-xl p-5">
      <RoomsFilters @update:filters="onFiltersChange" />
    </div>

    <RoomsTable
      :rooms="rooms"
      :is-loading="isLoading"
      :error="error"
      :page="page"
      :per-page="perPage"
      :total="total"
      :last-page="lastPage"
      @update:page="page = $event"
      @update:per-page="(v: number) => { perPage = v; page = 1 }"
      @edit="openEditModal"
      @delete="requestDelete"
    />

    <CreateRoomModal
      :is-open="isFormModalOpen"
      :room="editingRoom"
      @close="isFormModalOpen = false"
      @saved="onRoomSaved"
    />

    <ConfirmDialog
      :is-open="!!roomPendingDelete"
      title="Supprimer cette salle ?"
      :message="`Voulez-vous vraiment supprimer « ${roomPendingDelete?.nom} » ? Cette action est irréversible.`"
      :error="deleteError"
      confirm-label="Supprimer"
      :is-loading="isDeleting"
      @cancel="roomPendingDelete = null; upcomingReservationsForDelete = []"
      @confirm="confirmDelete"
    >
      <p v-if="isCheckingReservations" class="text-muted text-xs mt-3">
        Vérification des réservations en cours...
      </p>
      <div v-else-if="upcomingReservationsForDelete.length > 0" class="mt-3">
        <p class="text-red-400 text-xs font-medium mb-2">
          {{ upcomingReservationsForDelete.length }} réservation{{ upcomingReservationsForDelete.length > 1 ? 's' : '' }} à venir {{ upcomingReservationsForDelete.length > 1 ? 'seront' : 'sera' }} automatiquement annulée{{ upcomingReservationsForDelete.length > 1 ? 's' : '' }} et leurs organisateurs prévenus :
        </p>
        <ul class="space-y-1 max-h-32 overflow-y-auto">
          <li v-for="reservation in upcomingReservationsForDelete" :key="reservation.id" class="text-muted text-xs">
            {{ formatReservationDate(reservation) }} — {{ reservation.motif }}
            <span v-if="reservation.user">({{ reservation.user.prenom }} {{ reservation.user.nom }})</span>
          </li>
        </ul>
      </div>
    </ConfirmDialog>
  </div>
</template>
