<script setup lang="ts">
definePageMeta({ layout: 'default', middleware: 'auth' })

import { ref, computed, watch, onMounted } from 'vue'
import type { RawRoom } from '~/features/rooms/type'
import { useRoomService } from '~/features/rooms/services/room.service'
import type { RawEquipment } from '~/features/equipments/type'
import { useEquipmentService } from '~/features/equipments/services/equipment.service'
import type { RawReservation } from '~/features/reservations/type'
import { useReservationsService } from '~/features/reservations/services/reservations.service'
import type { RawReservationSetting } from '~/features/settings/type'
import { useReservationSettingService } from '~/features/settings/services/reservation-setting.service'

const roomService = useRoomService()
const equipmentService = useEquipmentService()
const reservationsService = useReservationsService()
const reservationSettingService = useReservationSettingService()
const authStore = useAuthStore()

// Le Super Employé n'a de visibilité que sur la semaine en cours, sans navigation.
const isSuperEmploye = computed(() => authStore.user?.role === 'Super_Employe')

function todayIso(): string {
  const d = new Date()
  return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`
}

function timeToMinutes(value: string): number {
  const [h, m] = value.split(':').map(Number)
  return (h ?? 0) * 60 + (m ?? 0)
}

const DAY_NAMES = ['SUNDAY', 'MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY']

function dayOfWeekFor(dateIso: string): string {
  const [y, m, d] = dateIso.split('-').map(Number)
  const date = new Date(y!, m! - 1, d!)
  return DAY_NAMES[date.getDay()]!
}

const selectedDate = ref(todayIso())
const searchQuery = ref('')

const rooms = ref<RawRoom[]>([])
const equipments = ref<RawEquipment[]>([])
const reservationSetting = ref<RawReservationSetting | null>(null)
const reservations = ref<RawReservation[]>([])

const isLoadingSchedule = ref(false)
const scheduleError = ref<string | null>(null)

const serviceHourForSelectedDate = computed(() => {
  if (!reservationSetting.value) return null
  const dow = dayOfWeekFor(selectedDate.value)
  return reservationSetting.value.service_hours.find(h => h.day_of_week === dow) ?? null
})

const isOpen = computed(() => serviceHourForSelectedDate.value?.is_open ?? false)
const dayStartMinutes = computed(() =>
  serviceHourForSelectedDate.value?.start_time ? timeToMinutes(serviceHourForSelectedDate.value.start_time) : 8 * 60,
)
const dayEndMinutes = computed(() =>
  serviceHourForSelectedDate.value?.end_time ? timeToMinutes(serviceHourForSelectedDate.value.end_time) : 18 * 60,
)

const bufferMinutes = computed(() => reservationSetting.value?.reservation_buffer ?? 0)

const filteredRooms = computed(() => {
  const query = searchQuery.value.trim().toLowerCase()
  if (!query) return rooms.value
  return rooms.value.filter(room => room.nom.toLowerCase().includes(query))
})

const filteredEquipments = computed(() => {
  const query = searchQuery.value.trim().toLowerCase()
  if (!query) return equipments.value
  return equipments.value.filter(equipment => equipment.nom.toLowerCase().includes(query))
})

async function loadReservations() {
  isLoadingSchedule.value = true
  scheduleError.value = null
  try {
    reservations.value = await reservationsService.getDayReservations(selectedDate.value)
  } catch {
    scheduleError.value = 'Impossible de charger les réservations de cette journée.'
    reservations.value = []
  } finally {
    isLoadingSchedule.value = false
  }
}

watch(selectedDate, loadReservations)

async function loadRooms() {
  try {
    rooms.value = (await roomService.list()).filter(room => room.type.value === 'MEETING')
  } catch {
    rooms.value = []
  }
}

async function loadEquipments() {
  try {
    equipments.value = (await equipmentService.paginate({ usage_type: 'EMPRUNTABLE', per_page: 100 })).data
  } catch {
    equipments.value = []
  }
}

async function loadReservationSetting() {
  try {
    reservationSetting.value = await reservationSettingService.get()
  } catch {
    reservationSetting.value = null
  }
}

onMounted(() => {
  // Les 4 appels doivent démarrer ensemble (pas les uns après les autres avec des
  // await séquentiels) : sinon le compteur de requêtes en cours (pendingRequests,
  // cf. useApi.ts) retombe brièvement à 0 dès que la 1ère requête finit, avant même
  // que les suivantes aient démarré. Le loader de page (app.vue) se cache alors trop
  // tôt, et la page continue visiblement de se remplir après sa disparition.
  loadReservations()
  loadRooms()
  loadEquipments()
  loadReservationSetting()
})
</script>

<template>
  <div class="space-y-6">
    <AvailabilityHeader />
    <AvailabilityStats v-if="!isSuperEmploye" />
    <AvailabilityNav v-if="!isSuperEmploye" v-model:selected-date="selectedDate" />
    <WeekDayStrip v-model:selected-date="selectedDate" />

    <AvailabilitySearch v-model="searchQuery" />

    <AvailabilityGrid
      :rooms="filteredRooms"
      :reservations="reservations"
      :day-start-minutes="dayStartMinutes"
      :day-end-minutes="dayEndMinutes"
      :buffer-minutes="bufferMinutes"
      :is-open="isOpen"
      :is-loading="isLoadingSchedule"
      :error="scheduleError"
    />

    <EquipmentAvailabilityGrid
      :equipments="filteredEquipments"
      :reservations="reservations"
      :day-start-minutes="dayStartMinutes"
      :day-end-minutes="dayEndMinutes"
      :buffer-minutes="bufferMinutes"
      :is-open="isOpen"
      :is-loading="isLoadingSchedule"
      :error="scheduleError"
    />
  </div>
</template>
