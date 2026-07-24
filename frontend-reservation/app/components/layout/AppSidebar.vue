<script setup lang="ts">
import {
  Home, Calendar, DoorOpen, Monitor, Users, Clock, Ticket,
  ShieldCheck, Settings, FileText, ChevronLeft, ShieldQuestion
} from 'lucide-vue-next'
import { ref } from 'vue'

const isCollapsed = ref(false)

const gestionLinks = [
  { icon: Home, label: 'Dashboard', to: '/dashboard' },
  { icon: Calendar, label: 'Réservations', to: '/reservations' },
  { icon: DoorOpen, label: 'Salles', to: '/rooms' },
  { icon: Monitor, label: 'Équipements', to: '/equipments' },
  { icon: Users, label: 'Utilisateurs', to: '/users' },
  { icon: Clock, label: 'Disponibilités', to: '/availability' },
  { icon: Ticket, label: 'Tickets', to: '/tickets' },
]

const adminLinks = [
  { icon: ShieldCheck, label: 'Administrateurs', to: '/administrators' },
  { icon: Settings, label: 'Paramètres', to: '/settings' },
  { icon: FileText, label: 'Journaux d\'activité', to: '/activity-logs' },
]
</script>

<template>
  <aside
    :class="isCollapsed ? 'w-20' : 'w-64'"
    class="h-screen sticky top-0 bg-surface border-r border-border flex flex-col transition-all duration-300"
  >
    <!-- Logo + collapse -->
    <div class="flex items-center justify-between px-5 h-16 border-b border-border">
      <div class="flex items-center gap-2 overflow-hidden">
       <img src="/images/logo/companyflow-logo.svg" alt="CompanyFlow" class="h-10 w-auto"/>
       <span class="text-xl font-bold tracking-tight text-white select-none">
      Company<span class="text-indigo-500">Flow</span>
    </span>
      </div>
      <button
        v-if="!isCollapsed"
        class="text-muted hover:text-foreground transition-colors shrink-0"
        @click="isCollapsed = true"
      >
        <ChevronLeft class="w-4 h-4" />
      </button>
      <button
        v-else
        class="text-muted hover:text-foreground transition-colors shrink-0"
        @click="isCollapsed = false"
      >
        <ChevronLeft class="w-4 h-4 rotate-180" />
      </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto px-3 py-5 space-y-6">
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
            >
              <component :is="link.icon" class="w-4.5 h-4.5 shrink-0" />
              <span v-if="!isCollapsed" class="whitespace-nowrap">{{ link.label }}</span>
            </NuxtLink>
          </li>
        </ul>
      </div>

      <div>
        <p v-if="!isCollapsed" class="text-muted text-xs font-semibold uppercase tracking-wide px-2 mb-2">
          Administration
        </p>
        <ul class="space-y-1">
          <li v-for="link in adminLinks" :key="link.to">
            <NuxtLink
              :to="link.to"
              class="flex items-center gap-3 px-2.5 py-2 rounded-lg text-muted text-sm hover:bg-border/50 hover:text-foreground transition-colors"
              active-class="bg-primary/15 text-primary-light font-medium"
            >
              <component :is="link.icon" class="w-4.5 h-4.5 shrink-0" />
              <span v-if="!isCollapsed" class="whitespace-nowrap">{{ link.label }}</span>
            </NuxtLink>
          </li>
        </ul>
      </div>
    </nav>

    <!-- Footer profil -->
    <div class="border-t border-border p-4">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-primary/15 flex items-center justify-center shrink-0">
          <ShieldQuestion class="w-4.5 h-4.5 text-primary-light" />
        </div>
        <div v-if="!isCollapsed" class="min-w-0">
          <p class="text-foreground text-sm font-medium truncate">Super Administrateur</p>
          <p class="text-muted text-xs truncate">Accès global</p>
        </div>
      </div>
    </div>
  </aside>
</template>