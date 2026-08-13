<script setup lang="ts">
definePageMeta({ layout: 'default', middleware: 'auth' })

import { onMounted, ref, watch } from 'vue'
import type { ApiError } from '~/composables/useApi'
import type { RawEquipmentCategory } from '~/features/equipment-categories/type'
import { useEquipmentCategoryService } from '~/features/equipment-categories/services/equipment-category.service'

const equipmentCategoryService = useEquipmentCategoryService()
const toast = useToast()

const search = ref('')
const page = ref(1)
const perPage = ref(10)

const categories = ref<RawEquipmentCategory[]>([])
const total = ref(0)
const lastPage = ref(1)
const isLoading = ref(false)
const error = ref<string | null>(null)

async function loadCategories() {
  isLoading.value = true
  error.value = null
  try {
    const response = await equipmentCategoryService.paginate({
      search: search.value || undefined,
      page: page.value,
      per_page: perPage.value,
    })
    categories.value = response.data
    total.value = response.total
    lastPage.value = response.last_page
  } catch {
    error.value = 'Impossible de charger les catégories.'
    categories.value = []
  } finally {
    isLoading.value = false
  }
}

watch(search, () => {
  page.value = 1
  loadCategories()
})
watch([page, perPage], loadCategories)
onMounted(loadCategories)

function onSearchChange(value: string) {
  search.value = value
}

const categoriesStatsRef = ref<{ refresh: () => void } | null>(null)

function refreshAfterChange() {
  loadCategories()
  categoriesStatsRef.value?.refresh()
}

// Création / édition
const isFormModalOpen = ref(false)
const editingCategory = ref<RawEquipmentCategory | null>(null)

function openCreateModal() {
  editingCategory.value = null
  isFormModalOpen.value = true
}

function openEditModal(category: RawEquipmentCategory) {
  editingCategory.value = category
  isFormModalOpen.value = true
}

function onCategorySaved() {
  isFormModalOpen.value = false
  page.value = 1
  refreshAfterChange()
}

// Détails
const isDetailsModalOpen = ref(false)
const categoryForDetails = ref<RawEquipmentCategory | null>(null)

function openDetailsModal(category: RawEquipmentCategory) {
  categoryForDetails.value = category
  isDetailsModalOpen.value = true
}

// Suppression
const categoryPendingDelete = ref<RawEquipmentCategory | null>(null)
const isDeleting = ref(false)
const deleteError = ref<string | null>(null)

function requestDelete(category: RawEquipmentCategory) {
  categoryPendingDelete.value = category
  deleteError.value = null
}

async function confirmDelete() {
  if (!categoryPendingDelete.value) return

  isDeleting.value = true
  deleteError.value = null
  try {
    await equipmentCategoryService.remove(categoryPendingDelete.value.id)
    categoryPendingDelete.value = null
    if (categories.value.length === 1 && page.value > 1) page.value -= 1
    refreshAfterChange()
    toast.success('Catégorie supprimée avec succès.')
  } catch (e) {
    deleteError.value = (e as ApiError).message ?? 'Impossible de supprimer cette catégorie.'
  } finally {
    isDeleting.value = false
  }
}
</script>

<template>
  <div class="space-y-6">
    <EquipmentCategoriesHeader @create-click="openCreateModal" />
    <EquipmentCategoriesStats ref="categoriesStatsRef" />

    <div class="bg-surface border border-border rounded-xl p-5">
      <EquipmentCategoriesSearch @update:search="onSearchChange" />
    </div>

    <EquipmentCategoriesTable
      :categories="categories"
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
      @details="openDetailsModal"
    />

    <CreateEquipmentCategoryModal
      :is-open="isFormModalOpen"
      :category="editingCategory"
      @close="isFormModalOpen = false"
      @saved="onCategorySaved"
    />

    <CategoryDetailsModal
      :is-open="isDetailsModalOpen"
      :category="categoryForDetails"
      @close="isDetailsModalOpen = false"
    />

    <ConfirmDialog
      :is-open="!!categoryPendingDelete"
      title="Supprimer cette catégorie ?"
      :message="`Voulez-vous vraiment supprimer « ${categoryPendingDelete?.nom} » ? Cette action est irréversible.`"
      :error="deleteError"
      confirm-label="Supprimer"
      :is-loading="isDeleting"
      @cancel="categoryPendingDelete = null"
      @confirm="confirmDelete"
    />
  </div>
</template>
