<script setup lang="ts">
definePageMeta({ layout: 'default', middleware: 'auth' })

import { ref, watch, onMounted, computed } from 'vue'
import type { RawRoom, RoomFilters } from '~/features/rooms/type'
import { useRoomService } from '~/features/rooms/services/room.service'
import type { ExportColumn, ExportRow } from '~/composables/useTableExport'

const roomService = useRoomService()

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
const isDeleting = ref(false)
const deleteError = ref<string | null>(null)

function requestDelete(room: RawRoom) {
  roomPendingDelete.value = room
  deleteError.value = null
}

async function confirmDelete() {
  if (!roomPendingDelete.value) return

  isDeleting.value = true
  deleteError.value = null
  try {
    await roomService.remove(roomPendingDelete.value.id)
    roomPendingDelete.value = null
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
      @cancel="roomPendingDelete = null"
      @confirm="confirmDelete"
    />
  </div>
</template>
