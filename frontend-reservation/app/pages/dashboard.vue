<script setup lang="ts">
definePageMeta({ layout: 'default', middleware: 'auth' })

import { Ticket, Monitor, Wrench } from 'lucide-vue-next'
import { useTicketService } from '~/features/tickets/services/ticket.service'
import { useEquipmentService } from '~/features/equipments/services/equipment.service'

const authStore = useAuthStore()
const userName = computed(() => authStore.user?.prenom ?? '')

// Le Super Employé n'a pas la permission voir_dashboard_global : les stats/alertes de
// gestion (StatsGrid, TodoWidget) et le journal d'activité (réservé au Super Admin) sont
// remplacés par un tableau de bord centré sur son activité personnelle.
const isSuperEmploye = computed(() => authStore.user?.role === 'Super_Employe')

// Le Technicien n'a aucun droit sur les salles/réservations/utilisateurs (donc pas
// voir_dashboard_global) : son tableau de bord se concentre sur les tickets, son
// cœur de métier, avec les mêmes statistiques que sa page Tickets.
const isTechnicien = computed(() => authStore.user?.role === 'Technicien')

// Le Super Admin n'a pas de card "à traiter" : la card Activité récente prend
// sa place dans la grille principale (au lieu de rester dans la colonne latérale).
const isSuperAdmin = computed(() => authStore.user?.role === 'Super_Administrateur')

// L'Admin n'a pas de card "à traiter" non plus : le calendrier prend sa place à côté
// de l'occupation des salles, et la colonne latérale (qui ne contenait que le calendrier)
// est retirée puisqu'elle serait sinon vide.
const isAdmin = computed(() => authStore.user?.role === 'Administrateur')

// Le dashboard du Technicien affiche un aperçu différent de sa page Tickets :
// le nombre de tickets créés ce mois-ci, et l'état global du parc matériel
// (total + en maintenance) plutôt que la répartition par statut des tickets.
interface TechnicienStatCard {
  icon: typeof Ticket
  iconBg: string
  value: number
  label: string
  subtext?: string
}

const technicienStats = ref<TechnicienStatCard[]>([])
const isLoadingTechnicienStats = ref(false)

async function loadTechnicienStats() {
  if (!isTechnicien.value) return
  isLoadingTechnicienStats.value = true
  try {
    const [ticketStats, equipmentStats] = await Promise.all([
      useTicketService().getStats(),
      useEquipmentService().getStats(),
    ])

    technicienStats.value = [
      { icon: Ticket, iconBg: 'bg-primary/20 text-primary-light', value: ticketStats.total_ce_mois, label: 'Tickets total', subtext: 'Ce mois' },
      { icon: Monitor, iconBg: 'bg-blue-500/20 text-blue-400', value: equipmentStats.total, label: 'Équipements total' },
      { icon: Wrench, iconBg: 'bg-orange-500/20 text-orange-500', value: equipmentStats.in_maintenance, label: 'Équipements en maintenance' },
    ]
  } catch {
    technicienStats.value = []
  } finally {
    isLoadingTechnicienStats.value = false
  }
}

onMounted(loadTechnicienStats)

// Pas de journal d'activité dans la colonne latérale pour l'Admin (n'y a pas accès)
// ni pour le Super Admin (déplacé dans la grille principale, à la place de "à traiter").
const showRecentActivity = computed(() => authStore.user?.role !== 'Administrateur' && !isSuperAdmin.value)
</script>

<template>
  <div class="space-y-6">
    <DashboardHeader
      :user-name="userName"
      :subtitle="isSuperEmploye
        ? 'Voici un aperçu de vos réservations et de la disponibilité des salles.'
        : isTechnicien
          ? 'Voici un aperçu des tickets à traiter.'
          : undefined"
    />

    <template v-if="isSuperEmploye">
      <AvailabilityStats hide-occupied />

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-surface border border-border rounded-xl p-5">
          <div>
            <h2 class="text-foreground font-semibold">Envie de réserver une salle ?</h2>
            <p class="text-muted text-sm mt-0.5">Consultez les disponibilités et créez votre réservation en quelques clics.</p>
          </div>
          <NuxtLink
            to="/reservations"
            class="bg-primary hover:bg-primary-hover text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors shrink-0 text-center"
          >
            Réserver une salle
          </NuxtLink>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-surface border border-border rounded-xl p-5">
          <div>
            <h2 class="text-foreground font-semibold">Un matériel en panne ?</h2>
            <p class="text-muted text-sm mt-0.5">Signalez le problème en créant un ticket pour qu'il soit pris en charge.</p>
          </div>
          <NuxtLink
            to="/tickets"
            class="bg-primary hover:bg-primary-hover text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors shrink-0 text-center"
          >
            Créer un ticket
          </NuxtLink>
        </div>
      </div>

      <UpcomingReservations />

      <CalendarWidget />
    </template>

    <template v-else-if="isTechnicien">
      <div v-if="isLoadingTechnicienStats" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <SkeletonStatCard v-for="i in 3" :key="i" />
      </div>
      <div v-else class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div v-for="stat in technicienStats" :key="stat.label" class="bg-surface border border-border rounded-xl p-5">
          <div class="w-11 h-11 rounded-xl flex items-center justify-center mb-3" :class="stat.iconBg">
            <component :is="stat.icon" class="w-5 h-5" />
          </div>
          <p class="text-foreground text-2xl font-bold">{{ stat.value }}</p>
          <p class="text-foreground text-sm">{{ stat.label }}</p>
          <p v-if="stat.subtext" class="text-muted text-xs mt-1">{{ stat.subtext }}</p>
        </div>
      </div>

      <RecentTicketsWidget />
    </template>

    <template v-else>
      <StatsGrid />

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne principale (2/3, pleine largeur pour l'Admin qui n'a pas de colonne latérale) -->
        <div :class="isAdmin ? 'lg:col-span-3 space-y-6' : 'lg:col-span-2 space-y-6'">
          <UpcomingReservations />

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <RoomOccupancy />
            <RecentActivity v-if="isSuperAdmin" />
            <CalendarWidget v-else-if="isAdmin" />
            <TodoWidget v-else />
          </div>
        </div>

        <!-- Colonne latérale (1/3) -->
        <div v-if="!isAdmin" class="space-y-6">
          <CalendarWidget />
          <RecentActivity v-if="showRecentActivity" />
        </div>
      </div>
    </template>
  </div>
</template>