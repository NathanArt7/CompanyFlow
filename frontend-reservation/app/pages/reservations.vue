<script setup lang="ts">
definePageMeta({ layout: 'default', middleware: 'auth' })

import { ref, reactive, watch, onMounted, computed } from 'vue'
import type { RawReservation, ReservationFilters } from '~/features/reservations/type'
import { useReservationsService } from '~/features/reservations/services/reservations.service'
import type { ExportColumn, ExportRow } from '~/composables/useTableExport'

const authStore = useAuthStore()

// Le Super Employé n'a pas de résumé de la journée (statistiques réservées aux rôles de gestion).
const isSuperEmploye = computed(() => authStore.user?.role === 'Super_Employe')

function todayIso(): string {
  const d = new Date()
  return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`
}

const reservationsService = useReservationsService()

const selectedDate = ref(todayIso())
const filters = reactive<ReservationFilters>({ roomId: null, status: null, userId: null })
const page = ref(1)
const perPage = ref(10)

const reservations = ref<RawReservation[]>([])
const total = ref(0)
const lastPage = ref(1)
const isLoading = ref(false)
const error = ref<string | null>(null)

async function loadReservations() {
  isLoading.value = true
  error.value = null
  try {
    const response = await reservationsService.getReservations({
      // La table reste toujours filtrée sur le jour sélectionné dans le WeekStrip.
      // Pour le Super Employé, seule la pagination disparaît (100 = maximum autorisé par
      // l'API, largement suffisant pour les réservations d'une seule journée).
      date: selectedDate.value,
      room_id: filters.roomId ?? undefined,
      status: filters.status ?? undefined,
      user_id: filters.userId ?? undefined,
      page: isSuperEmploye.value ? 1 : page.value,
      per_page: isSuperEmploye.value ? 100 : perPage.value,
      sort: 'date_reservation',
      direction: 'asc',
    })
    reservations.value = response.data
    total.value = response.total
    lastPage.value = response.last_page
  } catch {
    error.value = 'Impossible de charger les réservations.'
    reservations.value = []
  } finally {
    isLoading.value = false
  }
}

watch([selectedDate, () => filters.roomId, () => filters.status, () => filters.userId], () => {
  page.value = 1
  loadReservations()
})
watch([page, perPage], loadReservations)
onMounted(loadReservations)

const exportColumns: ExportColumn[] = [
  { key: 'heure', label: 'Heure' },
  { key: 'salle', label: 'Salle' },
  { key: 'evenement', label: 'Événement' },
  { key: 'organisateur', label: 'Organisateur' },
  { key: 'statut', label: 'Statut' },
  { key: 'equipements', label: 'Équipements' },
]

const exportRows = computed<ExportRow[]>(() => reservations.value.map(r => ({
  heure: `${r.heure_debut.slice(0, 5)} - ${r.heure_fin.slice(0, 5)}`,
  salle: r.room?.nom ?? '—',
  evenement: r.motif,
  organisateur: r.user ? `${r.user.prenom} ${r.user.nom}` : '—',
  statut: r.statut === 'ANNULEE' ? 'Annulée' : 'Confirmée',
  equipements: r.equipments.length > 0 ? r.equipments.map(eq => eq.nom).join(', ') : '—',
})))

function onFiltersChange(next: ReservationFilters) {
  filters.roomId = next.roomId
  filters.status = next.status
  filters.userId = next.userId
}

const weekStripRef = ref<{ refresh: () => void } | null>(null)
const dailySummaryRef = ref<{ refresh: () => void } | null>(null)

// Création / édition
const isFormModalOpen = ref(false)
const editingReservation = ref<RawReservation | null>(null)

function openCreateModal() {
  editingReservation.value = null
  isFormModalOpen.value = true
}

function openEditModal(reservation: RawReservation) {
  editingReservation.value = reservation
  isFormModalOpen.value = true
}

function onReservationSaved(saved: RawReservation) {
  isFormModalOpen.value = false
  selectedDate.value = saved.date_reservation.slice(0, 10)
  loadReservations()
  weekStripRef.value?.refresh()
  dailySummaryRef.value?.refresh()
}

// Détails
const isDetailsModalOpen = ref(false)
const reservationForDetails = ref<RawReservation | null>(null)

function openDetailsModal(reservation: RawReservation) {
  reservationForDetails.value = reservation
  isDetailsModalOpen.value = true
}

// Annulation
const reservationPendingCancel = ref<RawReservation | null>(null)
const isCancelling = ref(false)
const cancelError = ref<string | null>(null)

function requestCancel(reservation: RawReservation) {
  reservationPendingCancel.value = reservation
  cancelError.value = null
}

async function confirmCancel() {
  if (!reservationPendingCancel.value) return

  isCancelling.value = true
  cancelError.value = null
  try {
    await reservationsService.cancelReservation(reservationPendingCancel.value.id)
    reservationPendingCancel.value = null
    loadReservations()
    weekStripRef.value?.refresh()
    dailySummaryRef.value?.refresh()
  } catch (e) {
    cancelError.value = (e as { message?: string }).message ?? 'Impossible d\'annuler cette réservation.'
  } finally {
    isCancelling.value = false
  }
}
</script>

<template>
  <div class="space-y-6">
    <ReservationsHeader
      :export-columns="exportColumns"
      :export-rows="exportRows"
      @create-click="openCreateModal"
    />

    <div class="grid grid-cols-1 gap-6" :class="{ 'lg:grid-cols-2': !isSuperEmploye }">
      <WeekStrip ref="weekStripRef" v-model:selected-date="selectedDate" />
      <DailySummary v-if="!isSuperEmploye" ref="dailySummaryRef" :selected-date="selectedDate" />
    </div>

    <div v-if="!isSuperEmploye" class="bg-surface border border-border rounded-xl p-5">
      <ReservationsFilters :reservations="reservations" @update:filters="onFiltersChange" />
    </div>

    <ReservationsTable
      :reservations="reservations"
      :is-loading="isLoading"
      :error="error"
      :page="page"
      :per-page="perPage"
      :total="total"
      :last-page="lastPage"
      :hide-pagination="isSuperEmploye"
      :hide-organizer="isSuperEmploye"
      @update:page="page = $event"
      @update:per-page="(v: number) => { perPage = v; page = 1 }"
      @details="openDetailsModal"
      @edit="openEditModal"
      @cancel="requestCancel"
    />

    <CreateReservationModal
      :is-open="isFormModalOpen"
      :default-date="selectedDate"
      :reservation="editingReservation"
      @close="isFormModalOpen = false"
      @saved="onReservationSaved"
    />

    <ReservationDetailsModal
      :is-open="isDetailsModalOpen"
      :reservation="reservationForDetails"
      @close="isDetailsModalOpen = false"
    />

    <ConfirmDialog
      :is-open="!!reservationPendingCancel"
      title="Annuler cette réservation ?"
      :message="`Voulez-vous vraiment annuler « ${reservationPendingCancel?.motif} » ? Cette action est irréversible.`"
      :error="cancelError"
      confirm-label="Annuler la réservation"
      :is-loading="isCancelling"
      @cancel="reservationPendingCancel = null"
      @confirm="confirmCancel"
    />
  </div>
</template>
