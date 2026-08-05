<script setup lang="ts">
definePageMeta({ layout: 'default', middleware: 'auth' })

import { Plus } from 'lucide-vue-next'
import { ref, computed, watch, onMounted } from 'vue'
import type { RawTicket, TicketStats } from '~/features/tickets/type'
import { useTicketService } from '~/features/tickets/services/ticket.service'

const authStore = useAuthStore()
const ticketService = useTicketService()

const canCreate = computed(() => authStore.permissions.includes('creer_ticket'))

// Seul le Technicien a l'interface de gestion complète (stats, colonnes Type/
// Localisation/Créé par, actions de prise en charge/fermeture) et voit tous les
// tickets de l'entreprise. Tous les autres rôles (y compris Admin/Super Admin,
// qui gardent pourtant les permissions "consulter_tous_tickets"/"accepter_ticket"/
// "cloturer_ticket" — le Super Admin en particulier hérite de toutes les
// permissions métier — pour d'autres usages) n'ont que la liste de leurs
// propres tickets, sans actions, comme un Employé : cette page se base donc
// sur le rôle plutôt que sur ces permissions pour décider de l'interface.
const isTechnicienView = computed(() => authStore.user?.role === 'Technicien')
const canManageStatus = computed(() => isTechnicienView.value)

const page = ref(1)
const perPage = ref(10)

const tickets = ref<RawTicket[]>([])
const total = ref(0)
const lastPage = ref(1)
const isLoading = ref(false)
const error = ref<string | null>(null)

async function loadTickets() {
  isLoading.value = true
  error.value = null
  try {
    const response = await ticketService.getTickets({
      page: page.value,
      per_page: perPage.value,
      user_id: isTechnicienView.value ? undefined : authStore.user?.id,
    })
    tickets.value = response.data
    total.value = response.total
    lastPage.value = response.last_page
  } catch (e) {
    error.value = (e as { message?: string }).message ?? 'Impossible de charger les tickets.'
    tickets.value = []
  } finally {
    isLoading.value = false
  }
}

watch([page, perPage], loadTickets)
onMounted(loadTickets)

// Statistiques (technicien / admin / super admin)
const stats = ref<TicketStats | null>(null)
const isLoadingStats = ref(false)

async function loadStats() {
  if (!isTechnicienView.value) return
  isLoadingStats.value = true
  try {
    stats.value = await ticketService.getStats()
  } catch {
    stats.value = null
  } finally {
    isLoadingStats.value = false
  }
}

onMounted(loadStats)

// Création
const isCreateModalOpen = ref(false)

function onTicketSaved() {
  isCreateModalOpen.value = false
  page.value = 1
  loadTickets()
  loadStats()
}

// Prise en charge : Ouvert -> En cours, matériel -> En maintenance.
async function onAccept(ticket: RawTicket) {
  try {
    await ticketService.accept(ticket.id)
    loadTickets()
    loadStats()
  } catch {
    // Échec ponctuel (droits, etc.) : le statut affiché reste inchangé après rechargement.
  }
}

// Fermeture : ouvre le modal de choix de l'état du matériel.
const ticketPendingClose = ref<RawTicket | null>(null)

function onRequestClose(ticket: RawTicket) {
  ticketPendingClose.value = ticket
}

function onTicketClosed() {
  ticketPendingClose.value = null
  loadTickets()
  loadStats()
}
</script>

<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-foreground text-2xl md:text-3xl font-bold">Tickets</h1>
        <p class="text-muted text-sm mt-1">
          {{ isTechnicienView ? "Gérez les tickets d'assistance de votre entreprise." : "Suivez vos tickets d'assistance." }}
        </p>
      </div>

      <button
        v-if="canCreate"
        class="flex items-center gap-2 bg-primary hover:bg-primary-hover text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors shrink-0"
        @click="isCreateModalOpen = true"
      >
        <Plus class="w-4 h-4" />
        Nouveau ticket
      </button>
    </div>

    <TicketStatsCards v-if="isTechnicienView" :stats="stats" :is-loading="isLoadingStats" />

    <TicketsTable
      :tickets="tickets"
      :is-loading="isLoading"
      :error="error"
      :page="page"
      :per-page="perPage"
      :total="total"
      :last-page="lastPage"
      :show-creator="isTechnicienView"
      :show-actions="canManageStatus"
      @update:page="page = $event"
      @update:per-page="(v: number) => { perPage = v; page = 1 }"
      @accept="onAccept"
      @request-close="onRequestClose"
    />

    <CreateTicketModal
      :is-open="isCreateModalOpen"
      @close="isCreateModalOpen = false"
      @saved="onTicketSaved"
    />

    <CloseTicketModal
      :is-open="!!ticketPendingClose"
      :ticket="ticketPendingClose"
      @close="ticketPendingClose = null"
      @saved="onTicketClosed"
    />
  </div>
</template>
