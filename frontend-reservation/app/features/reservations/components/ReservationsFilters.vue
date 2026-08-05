<script setup lang="ts">
import { RotateCcw } from 'lucide-vue-next'
import { ref, computed, onMounted } from 'vue'
import type { RawReservation, ReservationFilters } from '../type'
import type { RawRoom } from '~/features/rooms/type'
import { useRoomService } from '~/features/rooms/services/room.service'

const props = defineProps<{
  reservations: RawReservation[]
}>()

const emit = defineEmits<{
  'update:filters': [ReservationFilters]
}>()

const roomService = useRoomService()

const rooms = ref<RawRoom[]>([])
const roomsAvailable = ref(true)

const selectedRoom = ref('all')
const selectedStatus = ref('all')
const selectedOrganizer = ref('all')

const organizerOptions = computed(() => {
  const map = new Map<number, string>()
  for (const reservation of props.reservations) {
    if (reservation.user) map.set(reservation.user.id, `${reservation.user.prenom} ${reservation.user.nom}`)
  }
  return Array.from(map.entries()).map(([id, name]) => ({ id, name }))
})

function emitFilters() {
  emit('update:filters', {
    roomId: selectedRoom.value === 'all' ? null : Number(selectedRoom.value),
    status: selectedStatus.value === 'all' ? null : selectedStatus.value as 'CONFIRMEE' | 'ANNULEE' | 'EN_COURS' | 'PASSEE',
    userId: selectedOrganizer.value === 'all' ? null : Number(selectedOrganizer.value),
  })
}

const resetFilters = () => {
  selectedRoom.value = 'all'
  selectedStatus.value = 'all'
  selectedOrganizer.value = 'all'
  emitFilters()
}

onMounted(async () => {
  try {
    rooms.value = (await roomService.list()).filter(room => room.type.value === 'MEETING')
  } catch {
    roomsAvailable.value = false
  }
})
</script>

<template>
  <div class="flex flex-col sm:flex-row sm:items-center gap-3 flex-wrap">
    <select
      v-model="selectedRoom"
      :disabled="!roomsAvailable"
      class="bg-background border border-border rounded-lg px-3 py-2 text-sm text-foreground focus:outline-none focus:border-primary transition-colors disabled:opacity-60"
      @change="emitFilters"
    >
      <option value="all">Toutes les salles</option>
      <option v-for="room in rooms" :key="room.id" :value="String(room.id)">
        {{ room.nom }}
      </option>
    </select>

    <select
      v-model="selectedStatus"
      class="bg-background border border-border rounded-lg px-3 py-2 text-sm text-foreground focus:outline-none focus:border-primary transition-colors"
      @change="emitFilters"
    >
      <option value="all">Tous les statuts</option>
      <option value="CONFIRMEE">Confirmée</option>
      <option value="EN_COURS">En cours</option>
      <option value="PASSEE">Passée</option>
      <option value="ANNULEE">Annulée</option>
    </select>

    <select
      v-model="selectedOrganizer"
      class="bg-background border border-border rounded-lg px-3 py-2 text-sm text-foreground focus:outline-none focus:border-primary transition-colors"
      @change="emitFilters"
    >
      <option value="all">Tous les organisateurs</option>
      <option v-for="organizer in organizerOptions" :key="organizer.id" :value="String(organizer.id)">
        {{ organizer.name }}
      </option>
    </select>

    <button
      class="flex items-center gap-1.5 text-muted hover:text-foreground text-sm transition-colors"
      @click="resetFilters"
    >
      <RotateCcw class="w-3.5 h-3.5" />
      Réinitialiser
    </button>
  </div>
</template>
