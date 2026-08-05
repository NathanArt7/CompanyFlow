<script setup lang="ts">
definePageMeta({ layout: 'default', middleware: 'auth' })

import { ref, watch, onMounted, computed } from 'vue'
import type { ApiError } from '~/composables/useApi'
import type { RawUser, UserFilters } from '~/features/users/type'
import { useUserService } from '~/features/users/services/user.service'
import type { ExportColumn, ExportRow } from '~/composables/useTableExport'

const userService = useUserService()

const filters = ref<UserFilters>({ search: '', roleId: null, actif: null })
const page = ref(1)
const perPage = ref(10)

const users = ref<RawUser[]>([])
const total = ref(0)
const lastPage = ref(1)
const isLoading = ref(false)
const error = ref<string | null>(null)

async function loadUsers() {
  isLoading.value = true
  error.value = null
  try {
    const response = await userService.paginate({
      search: filters.value.search || undefined,
      role_id: filters.value.roleId ?? undefined,
      actif: filters.value.actif ?? undefined,
      page: page.value,
      per_page: perPage.value,
      sort: 'nom',
      direction: 'asc',
    })
    users.value = response.data
    total.value = response.total
    lastPage.value = response.last_page
  } catch (e) {
    error.value = (e as ApiError).status === 403
      ? "Vous n'avez pas la permission de consulter les utilisateurs."
      : 'Impossible de charger les utilisateurs.'
    users.value = []
  } finally {
    isLoading.value = false
  }
}

watch(filters, () => {
  page.value = 1
  loadUsers()
}, { deep: true })
watch([page, perPage], loadUsers)
onMounted(loadUsers)

const exportColumns: ExportColumn[] = [
  { key: 'nom', label: 'Nom' },
  { key: 'prenom', label: 'Prénom' },
  { key: 'email', label: 'Email' },
  { key: 'role', label: 'Rôle' },
  { key: 'actif', label: 'Statut' },
]

const exportRows = computed<ExportRow[]>(() => users.value.map(u => ({
  nom: u.nom,
  prenom: u.prenom,
  email: u.email,
  role: u.role,
  actif: u.actif ? 'Actif' : 'Inactif',
})))

function onFiltersChange(next: UserFilters) {
  filters.value = next
}

const usersStatsRef = ref<{ refresh: () => void } | null>(null)

function refreshAfterChange() {
  loadUsers()
  usersStatsRef.value?.refresh()
}

// Création / édition
const isFormModalOpen = ref(false)
const editingUser = ref<RawUser | null>(null)

function openCreateModal() {
  editingUser.value = null
  isFormModalOpen.value = true
}

function openEditModal(user: RawUser) {
  editingUser.value = user
  isFormModalOpen.value = true
}

function onUserSaved() {
  isFormModalOpen.value = false
  page.value = 1
  refreshAfterChange()
}

// Activation / désactivation
const userPendingStatusChange = ref<RawUser | null>(null)
const isTogglingStatus = ref(false)
const toggleStatusError = ref<string | null>(null)

function requestToggleStatus(user: RawUser) {
  userPendingStatusChange.value = user
  toggleStatusError.value = null
}

async function confirmToggleStatus() {
  if (!userPendingStatusChange.value) return

  isTogglingStatus.value = true
  toggleStatusError.value = null
  try {
    await userService.updateStatus(userPendingStatusChange.value.id, !userPendingStatusChange.value.actif)
    userPendingStatusChange.value = null
    refreshAfterChange()
  } catch (e) {
    toggleStatusError.value = (e as ApiError).message ?? 'Impossible de mettre à jour le statut de ce compte.'
  } finally {
    isTogglingStatus.value = false
  }
}

// Suppression définitive
const userPendingDelete = ref<RawUser | null>(null)
const isDeleting = ref(false)
const deleteError = ref<string | null>(null)

function requestDelete(user: RawUser) {
  userPendingDelete.value = user
  deleteError.value = null
}

async function confirmDelete() {
  if (!userPendingDelete.value) return

  isDeleting.value = true
  deleteError.value = null
  try {
    await userService.remove(userPendingDelete.value.id)
    userPendingDelete.value = null
    if (users.value.length === 1 && page.value > 1) page.value -= 1
    refreshAfterChange()
  } catch (e) {
    deleteError.value = (e as ApiError).message ?? 'Impossible de supprimer cet utilisateur.'
  } finally {
    isDeleting.value = false
  }
}
</script>

<template>
  <div class="space-y-6">
    <UsersHeader
      :export-columns="exportColumns"
      :export-rows="exportRows"
      @create-click="openCreateModal"
    />
    <UsersStats ref="usersStatsRef" />

    <div class="bg-surface border border-border rounded-xl p-5">
      <UsersFilters @update:filters="onFiltersChange" />
    </div>

    <UsersTable
      :users="users"
      :is-loading="isLoading"
      :error="error"
      :page="page"
      :per-page="perPage"
      :total="total"
      :last-page="lastPage"
      @update:page="page = $event"
      @update:per-page="(v: number) => { perPage = v; page = 1 }"
      @edit="openEditModal"
      @toggle-status="requestToggleStatus"
      @delete="requestDelete"
    />

    <CreateUserModal
      :is-open="isFormModalOpen"
      :user="editingUser"
      @close="isFormModalOpen = false"
      @saved="onUserSaved"
    />

    <ConfirmDialog
      :is-open="!!userPendingStatusChange"
      :title="userPendingStatusChange?.actif ? 'Désactiver ce compte ?' : 'activer ce compte ?'"
      :message="userPendingStatusChange?.actif
        ? `Voulez-vous vraiment désactiver le compte de « ${userPendingStatusChange?.prenom} ${userPendingStatusChange?.nom} » ? Il ne pourra plus se connecter.`
        : `Voulez-vous activer le compte de « ${userPendingStatusChange?.prenom} ${userPendingStatusChange?.nom} » ?`"
      :error="toggleStatusError"
      :confirm-label="userPendingStatusChange?.actif ? 'Désactiver' : 'Activer'"
      :is-loading="isTogglingStatus"
      @cancel="userPendingStatusChange = null"
      @confirm="confirmToggleStatus"
    />

    <ConfirmDialog
      :is-open="!!userPendingDelete"
      title="Supprimer définitivement cet utilisateur ?"
      :message="`Voulez-vous vraiment supprimer « ${userPendingDelete?.prenom} ${userPendingDelete?.nom} » ? Cette action est irréversible.`"
      :error="deleteError"
      confirm-label="Supprimer"
      :is-loading="isDeleting"
      @cancel="userPendingDelete = null"
      @confirm="confirmDelete"
    />
  </div>
</template>
