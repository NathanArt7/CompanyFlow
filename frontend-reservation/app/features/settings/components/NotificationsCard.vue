<script setup lang="ts">
import { Bell } from 'lucide-vue-next'
import { ref, onMounted } from 'vue'
import type { NotificationPreferences } from '../type'
import { useNotificationPreferenceService } from '../services/notification-preference.service'

const notificationPreferenceService = useNotificationPreferenceService()

interface ToggleDef {
  key: keyof NotificationPreferences
  label: string
  description: string
}

const toggleDefs: ToggleDef[] = [
  { key: 'reservations', label: 'Notifications de réservation', description: 'Recevoir des notifications pour les nouvelles réservations' },
  { key: 'rappels', label: 'Rappels de réservation', description: 'Recevoir des rappels avant les réservations' },
  { key: 'systeme', label: 'Notifications système', description: 'Recevoir les notifications système importantes' },
  { key: 'rapports_hebdomadaires', label: 'Rapports hebdomadaires', description: 'Recevoir le résumé hebdomadaire par email' },
]

const preferences = ref<NotificationPreferences | null>(null)
const isLoading = ref(true)
const error = ref<string | null>(null)
const savingKey = ref<string | null>(null)

async function load() {
  isLoading.value = true
  error.value = null
  try {
    preferences.value = await notificationPreferenceService.get()
  } catch {
    error.value = 'Impossible de charger les préférences de notification.'
  } finally {
    isLoading.value = false
  }
}

async function toggle(key: keyof NotificationPreferences) {
  if (!preferences.value) return

  const previous = preferences.value[key]
  preferences.value[key] = !previous
  savingKey.value = key
  try {
    preferences.value = await notificationPreferenceService.update(preferences.value)
  } catch {
    preferences.value[key] = previous
    error.value = 'Impossible de mettre à jour cette préférence.'
  } finally {
    savingKey.value = null
  }
}

onMounted(load)
</script>

<template>
  <div class="bg-surface border border-border rounded-xl p-5">
    <div class="flex items-start gap-3 mb-4">
      <div class="w-9 h-9 rounded-lg bg-primary/15 flex items-center justify-center shrink-0">
        <Bell class="w-4.5 h-4.5 text-primary-light" />
      </div>
      <div>
        <h2 class="text-foreground font-semibold">Notifications</h2>
        <p class="text-muted text-xs mt-0.5">Gérez vos préférences de notification.</p>
      </div>
    </div>

    <p v-if="isLoading" class="text-muted text-xs">
      Chargement…
    </p>
    <p v-else-if="error && !preferences" class="text-red-400 text-xs">
      {{ error }}
    </p>
    <div v-else-if="preferences" class="space-y-4">
      <p v-if="error" class="text-red-400 text-xs">
        {{ error }}
      </p>
      <div v-for="def in toggleDefs" :key="def.key" class="flex items-center justify-between gap-4">
        <div class="min-w-0">
          <p class="text-foreground text-sm font-medium">{{ def.label }}</p>
          <p class="text-muted text-xs mt-0.5">{{ def.description }}</p>
        </div>
        <button
          class="w-11 h-6 rounded-full shrink-0 transition-colors relative disabled:opacity-60"
          :class="preferences[def.key] ? 'bg-primary' : 'bg-border'"
          :disabled="savingKey === def.key"
          @click="toggle(def.key)"
        >
          <span
            class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full bg-white transition-transform"
            :class="preferences[def.key] ? 'translate-x-5' : 'translate-x-0'"
          />
        </button>
      </div>
    </div>
  </div>
</template>
