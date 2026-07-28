<script setup lang="ts">
import { Bell } from 'lucide-vue-next'
import { ref } from 'vue'
import type { NotificationToggle } from '../types'

const toggles = ref<NotificationToggle[]>([
  { label: 'Notifications de réservation', description: 'Recevoir des notifications pour les nouvelles réservations', enabled: true },
  { label: 'Rappels de réservation', description: 'Recevoir des rappels avant les réservations', enabled: true },
  { label: 'Notifications système', description: 'Recevoir les notifications système importantes', enabled: true },
  { label: 'Rapports hebdomadaires', description: 'Recevoir le résumé hebdomadaire par email', enabled: false },
])
</script>

<template>
  <div class="bg-surface border border-border rounded-xl p-5">
    <div class="flex items-start justify-between mb-4">
      <div class="flex items-start gap-3">
        <div class="w-9 h-9 rounded-lg bg-primary/15 flex items-center justify-center shrink-0">
          <Bell class="w-4.5 h-4.5 text-primary-light" />
        </div>
        <div>
          <h2 class="text-foreground font-semibold">Notifications</h2>
          <p class="text-muted text-xs mt-0.5">Gérez vos préférences de notification.</p>
        </div>
      </div>
      <NuxtLink
        to="/settings/notifications"
        class="bg-background border border-border rounded-lg px-3 py-1.5 text-sm text-foreground hover:border-primary/40 transition-colors shrink-0"
      >
        Gérer
      </NuxtLink>
    </div>

    <div class="space-y-4">
      <div v-for="toggle in toggles" :key="toggle.label" class="flex items-center justify-between gap-4">
        <div class="min-w-0">
          <p class="text-foreground text-sm font-medium">{{ toggle.label }}</p>
          <p class="text-muted text-xs mt-0.5">{{ toggle.description }}</p>
        </div>
        <button
          class="w-11 h-6 rounded-full shrink-0 transition-colors relative"
          :class="toggle.enabled ? 'bg-primary' : 'bg-border'"
          @click="toggle.enabled = !toggle.enabled"
        >
          <span
            class="absolute top-0.5 w-5 h-5 rounded-full bg-white transition-transform"
            :class="toggle.enabled ? 'translate-x-5' : 'translate-x-0.5'"
          />
        </button>
      </div>
    </div>
  </div>
</template>