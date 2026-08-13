<script setup lang="ts">
definePageMeta({ layout: 'default', middleware: 'auth' })

import { ref, watch, onMounted, computed } from 'vue'
import type { EquipmentFilters, RawEquipment } from '~/features/equipments/type'
import { useEquipmentService } from '~/features/equipments/services/equipment.service'
import type { ExportColumn, ExportRow } from '~/composables/useTableExport'

const equipmentService = useEquipmentService()
const authStore = useAuthStore()
const toast = useToast()

const canFullEdit = computed(() => authStore.permissions.includes('modifier_materiel'))
const canEditState = computed(() => authStore.permissions.includes('modifier_etat_materiel'))
const canDelete = computed(() => authStore.permissions.includes('supprimer_materiel'))

const filters = ref<EquipmentFilters>({ search: '', categoryId: null, usageType: null, etat: null })
const page = ref(1)
const perPage = ref(10)

const equipments = ref<RawEquipment[]>([])
const total = ref(0)
const lastPage = ref(1)
const isLoading = ref(false)
const error = ref<string | null>(null)

async function loadEquipments() {
  isLoading.value = true
  error.value = null
  try {
    const response = await equipmentService.paginate({
      search: filters.value.search || undefined,
      category_id: filters.value.categoryId ?? undefined,
      usage_type: filters.value.usageType ?? undefined,
      etat: filters.value.etat ?? undefined,
      page: page.value,
      per_page: perPage.value,
      sort: 'nom',
      direction: 'asc',
    })
    equipments.value = response.data
    total.value = response.total
    lastPage.value = response.last_page
  } catch {
    error.value = 'Impossible de charger les équipements.'
    equipments.value = []
  } finally {
    isLoading.value = false
  }
}

watch(filters, () => {
  page.value = 1
  loadEquipments()
}, { deep: true })
watch([page, perPage], loadEquipments)
onMounted(loadEquipments)

const exportColumns: ExportColumn[] = [
  { key: 'nom', label: 'Nom' },
  { key: 'code', label: 'Code' },
  { key: 'categorie', label: 'Catégorie' },
  { key: 'marque', label: 'Marque' },
  { key: 'modele', label: 'Modèle' },
  { key: 'usage', label: "Type d'usage" },
  { key: 'etat', label: 'État' },
]

const usageTypeLabels: Record<string, string> = {
  EMPRUNTABLE: 'Empruntable',
  NON_EMPRUNTABLE: 'Non empruntable',
}

const exportRows = computed<ExportRow[]>(() => equipments.value.map(e => ({
  nom: e.nom,
  code: e.code,
  categorie: e.category?.nom ?? '—',
  marque: e.marque,
  modele: e.modele,
  usage: usageTypeLabels[e.usage_type] ?? e.usage_type,
  etat: e.etat,
})))

function onFiltersChange(next: EquipmentFilters) {
  filters.value = next
}

const equipmentsStatsRef = ref<{ refresh: () => void } | null>(null)

function refreshAfterChange() {
  loadEquipments()
  equipmentsStatsRef.value?.refresh()
}

// Création / édition
const isFormModalOpen = ref(false)
const editingEquipment = ref<RawEquipment | null>(null)

function openCreateModal() {
  editingEquipment.value = null
  isFormModalOpen.value = true
}

function openEditModal(equipment: RawEquipment) {
  editingEquipment.value = equipment
  isFormModalOpen.value = true
}

function onEquipmentSaved() {
  isFormModalOpen.value = false
  page.value = 1
  refreshAfterChange()
}

// Modification de l'état seul (Technicien)
const isStatusModalOpen = ref(false)
const editingEquipmentState = ref<RawEquipment | null>(null)

function openEditStateModal(equipment: RawEquipment) {
  editingEquipmentState.value = equipment
  isStatusModalOpen.value = true
}

function onEquipmentStatusSaved() {
  isStatusModalOpen.value = false
  refreshAfterChange()
}

// Suppression
const equipmentPendingDelete = ref<RawEquipment | null>(null)
const isDeleting = ref(false)
const deleteError = ref<string | null>(null)

function requestDelete(equipment: RawEquipment) {
  equipmentPendingDelete.value = equipment
  deleteError.value = null
}

async function confirmDelete() {
  if (!equipmentPendingDelete.value) return

  isDeleting.value = true
  deleteError.value = null
  try {
    await equipmentService.remove(equipmentPendingDelete.value.id)
    equipmentPendingDelete.value = null
    if (equipments.value.length === 1 && page.value > 1) page.value -= 1
    refreshAfterChange()
    toast.success('Équipement supprimé avec succès.')
  } catch {
    deleteError.value = 'Impossible de supprimer cet équipement.'
  } finally {
    isDeleting.value = false
  }
}
</script>

<template>
  <div class="space-y-6">
    <EquipmentsHeader
      :export-columns="exportColumns"
      :export-rows="exportRows"
      @create-click="openCreateModal"
    />
    <EquipmentsStats ref="equipmentsStatsRef" />

    <div class="bg-surface border border-border rounded-xl p-5">
      <EquipmentsFilters @update:filters="onFiltersChange" />
    </div>

    <EquipmentsTable
      :equipments="equipments"
      :is-loading="isLoading"
      :error="error"
      :page="page"
      :per-page="perPage"
      :total="total"
      :last-page="lastPage"
      :can-full-edit="canFullEdit"
      :can-edit-state="canEditState"
      :can-delete="canDelete"
      @update:page="page = $event"
      @update:per-page="(v: number) => { perPage = v; page = 1 }"
      @edit="openEditModal"
      @edit-state="openEditStateModal"
      @delete="requestDelete"
    />

    <CreateEquipmentModal
      :is-open="isFormModalOpen"
      :equipment="editingEquipment"
      @close="isFormModalOpen = false"
      @saved="onEquipmentSaved"
    />

    <UpdateEquipmentStatusModal
      :is-open="isStatusModalOpen"
      :equipment="editingEquipmentState"
      @close="isStatusModalOpen = false"
      @saved="onEquipmentStatusSaved"
    />

    <ConfirmDialog
      :is-open="!!equipmentPendingDelete"
      title="Supprimer cet équipement ?"
      :message="`Voulez-vous vraiment supprimer « ${equipmentPendingDelete?.nom} » ? Cette action est irréversible.`"
      :error="deleteError"
      confirm-label="Supprimer"
      :is-loading="isDeleting"
      @cancel="equipmentPendingDelete = null"
      @confirm="confirmDelete"
    />
  </div>
</template>
