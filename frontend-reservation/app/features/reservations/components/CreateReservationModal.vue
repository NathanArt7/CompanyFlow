<script setup lang="ts">
import { X, Loader2 } from 'lucide-vue-next'
import { ref, computed, watch } from 'vue'
import type { AvailableEquipment, RawReservation } from '../type'
import { useReservationsService } from '../services/reservations.service'
import type { RawRoom } from '~/features/rooms/type'
import { useRoomService } from '~/features/rooms/services/room.service'

const props = defineProps<{
  isOpen: boolean
  defaultDate: string
  reservation?: RawReservation | null
}>()

const emit = defineEmits<{
  close: []
  saved: [RawReservation]
}>()

const isEditing = computed(() => !!props.reservation)

const reservationsService = useReservationsService()
const roomService = useRoomService()
const toast = useToast()

const rooms = ref<RawRoom[]>([])
const roomsError = ref(false)
const roomsLoaded = ref(false)

function todayDateString() {
  const now = new Date()
  return `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`
}

function nowTimeString() {
  const now = new Date()
  return `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`
}

const form = ref({
  roomId: null as number | null,
  motif: '',
  nombreParticipants: 1,
  dateReservation: props.defaultDate,
  heureDebut: '09:00',
  heureFin: '10:00',
  equipmentIds: [] as number[],
})

const equipments = ref<AvailableEquipment[]>([])
const isLoadingEquipments = ref(false)
const equipmentsError = ref(false)

const isSubmitting = ref(false)
const errorMessage = ref('')

function resetForm() {
  if (props.reservation) {
    form.value = {
      roomId: props.reservation.room_id,
      motif: props.reservation.motif,
      nombreParticipants: props.reservation.nombre_participants,
      dateReservation: props.reservation.date_reservation.slice(0, 10),
      heureDebut: props.reservation.heure_debut.slice(0, 5),
      heureFin: props.reservation.heure_fin.slice(0, 5),
      equipmentIds: props.reservation.equipments.map(eq => eq.id),
    }
  } else {
    form.value = {
      roomId: null,
      motif: '',
      nombreParticipants: 1,
      dateReservation: props.defaultDate,
      heureDebut: '09:00',
      heureFin: '10:00',
      equipmentIds: [],
    }
  }
  errorMessage.value = ''
  equipments.value = []
  activeCategoryId.value = null
}

async function loadRooms() {
  // Ce composant reste toujours monté (Teleport), même quand la modale est fermée :
  // on ne charge donc les salles qu'à la première ouverture, pas dès le montage,
  // pour éviter un appel /rooms inutile (en doublon avec ReservationsFilters) à
  // chaque chargement de la page réservations.
  if (roomsLoaded.value) return
  try {
    rooms.value = (await roomService.list()).filter(room => room.type.value === 'MEETING')
    roomsLoaded.value = true
  } catch {
    roomsError.value = true
  }
}

async function loadEquipments() {
  if (!form.value.dateReservation || !form.value.heureDebut || !form.value.heureFin) return
  if (form.value.heureFin <= form.value.heureDebut) return

  isLoadingEquipments.value = true
  equipmentsError.value = false
  try {
    equipments.value = await reservationsService.getAvailableEquipments(
      form.value.dateReservation,
      form.value.heureDebut,
      form.value.heureFin,
      props.reservation?.id,
    )
    const availableIds = new Set(equipments.value.filter(eq => eq.disponible).map(eq => eq.id))
    form.value.equipmentIds = form.value.equipmentIds.filter(id => availableIds.has(id))
  } catch {
    equipments.value = []
    equipmentsError.value = true
  } finally {
    isLoadingEquipments.value = false
  }
}

watch(() => [form.value.dateReservation, form.value.heureDebut, form.value.heureFin], loadEquipments)

watch(() => props.isOpen, (open) => {
  if (open) {
    resetForm()
    loadEquipments()
    loadRooms()
  }
})

function toggleEquipment(id: number) {
  const index = form.value.equipmentIds.indexOf(id)
  if (index === -1) form.value.equipmentIds.push(id)
  else form.value.equipmentIds.splice(index, 1)
}

interface EquipmentCategoryGroup {
  id: number
  nom: string
  equipments: AvailableEquipment[]
}

const equipmentCategories = computed<EquipmentCategoryGroup[]>(() => {
  const groups = new Map<number, EquipmentCategoryGroup>()

  for (const equipment of equipments.value) {
    if (equipment.category_id === null) continue

    if (!groups.has(equipment.category_id)) {
      groups.set(equipment.category_id, {
        id: equipment.category_id,
        nom: equipment.category_nom ?? 'Catégorie',
        equipments: [],
      })
    }

    groups.get(equipment.category_id)!.equipments.push(equipment)
  }

  return Array.from(groups.values()).sort((a, b) => a.nom.localeCompare(b.nom))
})

function selectedCountFor(category: EquipmentCategoryGroup) {
  return category.equipments.filter(eq => form.value.equipmentIds.includes(eq.id)).length
}

const activeCategoryId = ref<number | null>(null)

const activeCategory = computed(() =>
  equipmentCategories.value.find(category => category.id === activeCategoryId.value) ?? null,
)

function openCategory(id: number) {
  activeCategoryId.value = id
}

async function handleSubmit() {
  errorMessage.value = ''

  if (!form.value.roomId) {
    errorMessage.value = 'Merci de sélectionner une salle.'
    return
  }
  if (!form.value.motif) {
    errorMessage.value = 'Merci de renseigner un motif.'
    return
  }
  if (form.value.nombreParticipants < 1) {
    errorMessage.value = 'Le nombre de participants doit être au moins 1.'
    return
  }
  if (!form.value.dateReservation || !form.value.heureDebut || !form.value.heureFin) {
    errorMessage.value = 'Merci de renseigner la date et les horaires.'
    return
  }
  if (form.value.heureFin <= form.value.heureDebut) {
    errorMessage.value = "L'heure de fin doit être après l'heure de début."
    return
  }
  if (form.value.dateReservation < todayDateString()) {
    errorMessage.value = 'Impossible de créer une réservation à une date déjà passée.'
    return
  }
  if (form.value.dateReservation === todayDateString() && form.value.heureDebut < nowTimeString()) {
    errorMessage.value = 'Impossible de créer une réservation sur un créneau déjà passé.'
    return
  }

  const payload = {
    room_id: form.value.roomId,
    motif: form.value.motif,
    nombre_participants: form.value.nombreParticipants,
    date_reservation: form.value.dateReservation,
    heure_debut: form.value.heureDebut,
    heure_fin: form.value.heureFin,
    equipments: form.value.equipmentIds.length > 0 ? form.value.equipmentIds : undefined,
  }

  isSubmitting.value = true
  try {
    const saved = isEditing.value
      ? await reservationsService.updateReservation(props.reservation!.id, payload)
      : await reservationsService.createReservation(payload)
    emit('saved', saved)
    emit('close')
    toast.success(isEditing.value ? 'Réservation modifiée avec succès.' : 'Réservation créée avec succès.')
  } catch (e) {
    errorMessage.value = (e as { message?: string }).message ?? 'Une erreur est survenue. Veuillez réessayer.'
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <Teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60"
      @click.self="emit('close')"
    >
      <div class="bg-surface border border-border rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-border">
          <h2 class="text-foreground text-lg font-semibold">
            {{ isEditing ? 'Modifier la réservation' : 'Nouvelle réservation' }}
          </h2>
          <button class="text-muted hover:text-foreground transition-colors" @click="emit('close')">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form class="px-6 py-5 space-y-4" @submit.prevent="handleSubmit">
          <div>
            <label class="text-foreground text-sm font-medium block mb-2">Salle</label>
            <select
              v-model="form.roomId"
              :disabled="roomsError"
              class="w-full bg-background border border-border rounded-lg px-3 py-2.5 text-foreground text-sm focus:outline-none focus:border-primary transition-colors disabled:opacity-60"
            >
              <option :value="null">Sélectionner une salle</option>
              <option v-for="room in rooms" :key="room.id" :value="room.id">
                {{ room.nom }} ({{ room.capacite }} places){{ room.statut.value !== 'Disponible' ? ` [${room.statut.label}]` : '' }}
              </option>
            </select>
            <p v-if="roomsError" class="text-muted text-xs mt-1">
              Impossible de charger la liste des salles.
            </p>
          </div>

          <div>
            <label class="text-foreground text-sm font-medium block mb-2">Motif</label>
            <input
              v-model="form.motif"
              type="text"
              placeholder="Réunion d'équipe, formation..."
              class="w-full bg-background border border-border rounded-lg px-3 py-2.5 text-foreground placeholder:text-muted/60 text-sm focus:outline-none focus:border-primary transition-colors"
            >
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="text-foreground text-sm font-medium block mb-2">Date</label>
              <input
                v-model="form.dateReservation"
                type="date"
                :min="todayDateString()"
                class="w-full bg-background border border-border rounded-lg px-3 py-2.5 text-foreground text-sm focus:outline-none focus:border-primary transition-colors"
              >
            </div>
            <div>
              <label class="text-foreground text-sm font-medium block mb-2">Participants</label>
              <input
                v-model.number="form.nombreParticipants"
                type="number"
                min="1"
                class="w-full bg-background border border-border rounded-lg px-3 py-2.5 text-foreground text-sm focus:outline-none focus:border-primary transition-colors"
              >
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="text-foreground text-sm font-medium block mb-2">Heure de début</label>
              <input
                v-model="form.heureDebut"
                type="time"
                :min="form.dateReservation === todayDateString() ? nowTimeString() : undefined"
                class="w-full bg-background border border-border rounded-lg px-3 py-2.5 text-foreground text-sm focus:outline-none focus:border-primary transition-colors"
              >
            </div>
            <div>
              <label class="text-foreground text-sm font-medium block mb-2">Heure de fin</label>
              <input
                v-model="form.heureFin"
                type="time"
                class="w-full bg-background border border-border rounded-lg px-3 py-2.5 text-foreground text-sm focus:outline-none focus:border-primary transition-colors"
              >
            </div>
          </div>

          <div>
            <label class="text-foreground text-sm font-medium block mb-2">
              Équipements <span class="text-muted font-normal">(optionnel)</span>
            </label>
            <p v-if="isLoadingEquipments" class="text-muted text-xs">
              Chargement…
            </p>
            <p v-else-if="equipmentsError" class="text-red-400 text-xs">
              Impossible de charger les équipements disponibles.
            </p>
            <p v-else-if="equipmentCategories.length === 0" class="text-muted text-xs">
              Aucun équipement disponible pour ce créneau.
            </p>
            <div v-else class="flex flex-wrap gap-2">
              <button
                v-for="category in equipmentCategories"
                :key="category.id"
                type="button"
                class="flex items-center gap-1.5 bg-background border border-border rounded-lg px-3 py-2 text-sm text-foreground hover:border-primary/40 transition-colors"
                @click="openCategory(category.id)"
              >
                {{ category.nom }}
                <span class="text-muted text-xs">({{ category.equipments.length }})</span>
                <span
                  v-if="selectedCountFor(category) > 0"
                  class="bg-primary text-white text-xs font-medium rounded-full w-4.5 h-4.5 flex items-center justify-center"
                >
                  {{ selectedCountFor(category) }}
                </span>
              </button>
            </div>
          </div>

          <p v-if="errorMessage" class="text-red-400 text-sm">
            {{ errorMessage }}
          </p>

          <div class="flex items-center gap-3 pt-2">
            <button
              type="button"
              class="flex-1 bg-background hover:bg-border text-foreground font-medium py-2.5 rounded-lg border border-border transition-colors"
              @click="emit('close')"
            >
              Annuler
            </button>
            <button
              type="submit"
              :disabled="isSubmitting"
              class="flex-1 flex items-center justify-center gap-2 bg-primary hover:bg-primary-hover disabled:opacity-60 disabled:cursor-not-allowed text-white font-medium py-2.5 rounded-lg transition-colors"
            >
              <Loader2 v-if="isSubmitting" class="w-4 h-4 animate-spin" />
              {{ isSubmitting ? 'Enregistrement...' : (isEditing ? 'Enregistrer' : 'Créer la réservation') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <SelectEquipmentPanel
      :is-open="!!activeCategory"
      :category-name="activeCategory?.nom ?? ''"
      :equipments="activeCategory?.equipments ?? []"
      :selected-ids="form.equipmentIds"
      @close="activeCategoryId = null"
      @toggle="toggleEquipment"
    />
  </Teleport>
</template>
