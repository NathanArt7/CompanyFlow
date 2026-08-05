<script setup lang="ts">
import { Clock } from 'lucide-vue-next'
import { computed, onMounted, ref } from 'vue'
import type { RawReservationSetting } from '../type'
import { useReservationSettingService } from '../services/reservation-setting.service'

const reservationSettingService = useReservationSettingService()
const authStore = useAuthStore()

const canManage = computed(() => authStore.permissions.includes('configurer_systeme'))

const dayLabels: Record<string, string> = {
  MONDAY: 'Lundi',
  TUESDAY: 'Mardi',
  WEDNESDAY: 'Mercredi',
  THURSDAY: 'Jeudi',
  FRIDAY: 'Vendredi',
  SATURDAY: 'Samedi',
  SUNDAY: 'Dimanche',
}

const dayOrder = ['MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY', 'SUNDAY']

const setting = ref<RawReservationSetting | null>(null)
const isLoading = ref(true)
const error = ref<string | null>(null)
const isModalOpen = ref(false)

const orderedHours = computed(() => {
  if (!setting.value) return []
  return [...setting.value.service_hours].sort(
    (a, b) => dayOrder.indexOf(a.day_of_week) - dayOrder.indexOf(b.day_of_week),
  )
})

async function load() {
  isLoading.value = true
  error.value = null
  try {
    setting.value = await reservationSettingService.get()
  } catch {
    error.value = 'Impossible de charger les horaires.'
  } finally {
    isLoading.value = false
  }
}

function onSaved(updated: RawReservationSetting) {
  setting.value = updated
  isModalOpen.value = false
}

onMounted(load)
</script>

<template>
  <div class="bg-surface border border-border rounded-xl p-5">
    <div class="flex items-start justify-between mb-4">
      <div class="flex items-start gap-3">
        <div class="w-9 h-9 rounded-lg bg-primary/15 flex items-center justify-center shrink-0">
          <Clock class="w-4.5 h-4.5 text-primary-light" />
        </div>
        <div>
          <h2 class="text-foreground font-semibold">Horaires de service</h2>
          <p class="text-muted text-xs mt-0.5">Définissez les horaires de disponibilité pour les réservations.</p>
        </div>
      </div>
      <button
        v-if="!isLoading && !error && canManage"
        class="bg-background border border-border rounded-lg px-3 py-1.5 text-sm text-foreground hover:border-primary/40 transition-colors shrink-0"
        @click="isModalOpen = true"
      >
        Gérer
      </button>
    </div>

    <div v-if="isLoading" class="space-y-2.5">
      <div v-for="i in 7" :key="i" class="flex items-center justify-between">
        <Skeleton class="h-4 w-16" />
        <Skeleton class="h-4 w-24" />
      </div>
    </div>
    <p v-else-if="error" class="text-red-400 text-xs">
      {{ error }}
    </p>
    <div v-else class="space-y-2.5">
      <div v-for="row in orderedHours" :key="row.id" class="flex items-center justify-between text-sm">
        <div class="flex items-center gap-2">
          <Clock class="w-3.5 h-3.5" :class="row.is_open ? 'text-muted' : 'text-muted/50'" />
          <span class="text-foreground">{{ dayLabels[row.day_of_week] }}</span>
        </div>
        <span :class="row.is_open ? 'text-foreground' : 'text-red-400'">
          {{ row.is_open ? `${row.start_time?.slice(0, 5)} - ${row.end_time?.slice(0, 5)}` : 'Fermé' }}
        </span>
      </div>

      <div v-if="setting" class="flex items-center justify-between text-sm pt-2.5 mt-1 border-t border-border">
        <span class="text-foreground">Délai minimum entre deux réservations</span>
        <span class="text-foreground">{{ setting.reservation_buffer }} min</span>
      </div>
    </div>

    <EditBusinessHoursModal
      :is-open="isModalOpen"
      :setting="setting"
      @close="isModalOpen = false"
      @saved="onSaved"
    />
  </div>
</template>
