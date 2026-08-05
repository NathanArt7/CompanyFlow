<script setup lang="ts">
import {
  Home, Calendar, DoorOpen, Monitor, Users, Clock, Ticket,
  Settings, FileText, ChevronLeft, X
} from 'lucide-vue-next'
import { computed } from 'vue'

const { isCollapsed, isMobileOpen, closeMobile } = useSidebar()
const authStore = useAuthStore()

type NavLink = { icon: typeof Home, label: string, to: string }

const allLinks: Record<string, NavLink> = {
  dashboard: { icon: Home, label: 'Dashboard', to: '/dashboard' },
  reservations: { icon: Calendar, label: 'Réservations', to: '/reservations' },
  rooms: { icon: DoorOpen, label: 'Salles', to: '/rooms' },
  equipments: { icon: Monitor, label: 'Équipements', to: '/equipments' },
  users: { icon: Users, label: 'Utilisateurs', to: '/users' },
  availability: { icon: Clock, label: 'Disponibilités', to: '/availability' },
  tickets: { icon: Ticket, label: 'Tickets', to: '/tickets' },
}

const defaultGestionKeys = ['dashboard', 'reservations', 'rooms', 'equipments', 'users', 'availability', 'tickets']

// Le Super Employé n'a pas de droits de gestion sur les salles/équipements/utilisateurs :
// seuls les liens qu'il peut réellement utiliser apparaissent dans sa sidebar.
// Le Technicien n'a, lui, aucun droit sur les salles/réservations/utilisateurs/disponibilités,
// mais accède aux Équipements en lecture + changement d'état (depuis un ticket).
// L'Employé n'a accès qu'aux tickets (créer/suivre les siens) ; Paramètres reste
// disponible séparément via le lien autonome plus bas, comme pour tous les rôles.
const roleGestionKeys: Record<string, string[]> = {
  Super_Employe: ['dashboard', 'reservations', 'availability', 'tickets'],
  Technicien: ['dashboard', 'equipments', 'tickets'],
  Employe: ['tickets'],
}

const gestionLinks = computed(() => {
  const role = authStore.user?.role
  const keys = (role && roleGestionKeys[role]) ?? defaultGestionKeys
  return keys.map(key => allLinks[key])
})

// Seul le Super Admin a accès aux journaux d'activité et à une section "Administration"
// dédiée ; tous les autres rôles n'ont que le lien Paramètres, sans regroupement.
const isSuperAdmin = computed(() => authStore.user?.role === 'Super_Administrateur')

const adminLinks = computed(() => {
  if (!isSuperAdmin.value) return []

  return [
    { icon: Settings, label: 'Paramètres', to: '/settings' },
    { icon: FileText, label: 'Journaux d\'activité', to: '/activity-logs' },
  ]
})

const settingsLink = { icon: Settings, label: 'Paramètres', to: '/settings' }
</script>

<template>
  <!-- Fond assombri (mobile uniquement, tiroir ouvert) -->
  <div
    v-if="isMobileOpen"
    class="fixed inset-0 z-30 bg-black/60 lg:hidden"
    @click="closeMobile"
  />

  <aside
    :class="[
      isCollapsed ? 'lg:w-20' : 'lg:w-64',
      isMobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
    ]"
    class="w-64 h-screen fixed lg:sticky top-0 left-0 z-40 bg-surface border-r border-border flex flex-col transition-all duration-300"
  >
    <!-- Logo + collapse -->
    <div class="flex items-center justify-between px-5 h-16 border-b border-border">
      <div class="flex items-center gap-2 overflow-hidden">
       <img src="/images/logo/companyflow-logo.svg" alt="CompanyPilot" class="h-10 w-auto"/>
       <span class="text-xl font-bold tracking-tight text-foreground select-none">
      Company<span class="text-indigo-500">Pilot</span>
    </span>
      </div>
      <button
        v-if="!isCollapsed"
        class="hidden lg:block text-muted hover:text-foreground transition-colors shrink-0"
        @click="isCollapsed = true"
      >
        <ChevronLeft class="w-4 h-4" />
      </button>
      <button
        v-else
        class="hidden lg:block text-muted hover:text-foreground transition-colors shrink-0"
        @click="isCollapsed = false"
      >
        <ChevronLeft class="w-4 h-4 rotate-180" />
      </button>
      <button
        class="lg:hidden text-muted hover:text-foreground transition-colors shrink-0"
        @click="closeMobile"
      >
        <X class="w-5 h-5" />
      </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto px-3 py-5 space-y-3">
      <div>
        <p v-if="!isCollapsed" class="text-muted text-xs font-semibold uppercase tracking-wide px-2 mb-2">
          Gestion
        </p>
        <ul class="space-y-1">
          <li v-for="link in gestionLinks" :key="link.to">
            <NuxtLink
              :to="link.to"
              class="flex items-center gap-3 px-2.5 py-2 rounded-lg text-sm transition-colors"
              active-class="bg-primary/15 text-primary-light font-medium"
              exact-active-class="bg-primary/15 text-primary-light font-medium"
              @click="closeMobile"
            >
              <component :is="link.icon" class="w-4.5 h-4.5 shrink-0" />
              <span v-if="!isCollapsed" class="whitespace-nowrap">{{ link.label }}</span>
            </NuxtLink>
          </li>
        </ul>
      </div>

      <div v-if="adminLinks.length > 0">
        <p v-if="!isCollapsed" class="text-muted text-xs font-semibold uppercase tracking-wide px-2 mb-2">
          Administration
        </p>
        <ul class="space-y-1">
          <li v-for="link in adminLinks" :key="link.to">
            <NuxtLink
              :to="link.to"
              class="flex items-center gap-3 px-2.5 py-2 rounded-lg text-muted text-sm hover:bg-border/50 hover:text-foreground transition-colors"
              active-class="bg-primary/15 text-primary-light font-medium"
              @click="closeMobile"
            >
              <component :is="link.icon" class="w-4.5 h-4.5 shrink-0" />
              <span v-if="!isCollapsed" class="whitespace-nowrap">{{ link.label }}</span>
            </NuxtLink>
          </li>
        </ul>
      </div>

      <div v-else>
        <ul class="space-y-1">
          <li>
            <NuxtLink
              :to="settingsLink.to"
              class="flex items-center gap-3 px-2.5 py-2 rounded-lg text-muted text-sm hover:bg-border/50 hover:text-foreground transition-colors"
              active-class="bg-primary/15 text-primary-light font-medium"
              @click="closeMobile"
            >
              <component :is="settingsLink.icon" class="w-4.5 h-4.5 shrink-0" />
              <span v-if="!isCollapsed" class="whitespace-nowrap">{{ settingsLink.label }}</span>
            </NuxtLink>
          </li>
        </ul>
      </div>
    </nav>

  </aside>
</template>
