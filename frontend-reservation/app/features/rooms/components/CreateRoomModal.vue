<script setup lang="ts">
import { X, Loader2 } from 'lucide-vue-next'
import { ref, computed, watch } from 'vue'
import type { ApiError } from '~/composables/useApi'
import type { RawRoom } from '../type'
import { useRoomService } from '../services/room.service'

const props = defineProps<{
  isOpen: boolean
  room?: RawRoom | null
}>()

const emit = defineEmits<{
  close: []
  saved: [RawRoom]
}>()

const roomService = useRoomService()

const isEditing = computed(() => !!props.room)

const meetingStatuses = ['Disponible', 'Occupée', 'En maintenance', 'Hors service'] as const
const storageStatuses = ['Disponible', 'En maintenance', 'Hors service'] as const

const availableStatuses = computed(() => form.value.type === 'MEETING' ? meetingStatuses : storageStatuses)

const form = ref({
  nom: '',
  type: 'MEETING' as 'MEETING' | 'STORAGE',
  code: '',
  capacite: 10 as number | null,
  localisation: '',
  description: '',
  statut: 'Disponible' as 'Disponible' | 'Occupée' | 'En maintenance' | 'Hors service',
})

const isSubmitting = ref(false)
const errorMessage = ref('')

function resetForm() {
  if (props.room) {
    form.value = {
      nom: props.room.nom,
      type: props.room.type.value as 'MEETING' | 'STORAGE',
      code: props.room.code,
      capacite: props.room.capacite,
      localisation: props.room.localisation,
      description: props.room.description ?? '',
      statut: props.room.statut.value as 'Disponible' | 'Occupée' | 'En maintenance' | 'Hors service',
    }
  } else {
    form.value = {
      nom: '',
      type: 'MEETING',
      code: '',
      capacite: 10,
      localisation: '',
      description: '',
      statut: 'Disponible',
    }
  }
  errorMessage.value = ''
}

watch(() => props.isOpen, (open) => {
  if (open) resetForm()
})

watch(() => form.value.type, (type) => {
  if (type === 'STORAGE') form.value.capacite = null
  else if (form.value.capacite === null) form.value.capacite = 10

  if (!availableStatuses.value.includes(form.value.statut as never)) {
    form.value.statut = 'Disponible'
  }
})

async function handleSubmit() {
  errorMessage.value = ''

  if (!form.value.nom) {
    errorMessage.value = 'Merci de renseigner un nom.'
    return
  }
  if (!form.value.code) {
    errorMessage.value = 'Merci de renseigner un code.'
    return
  }
  if (!form.value.localisation) {
    errorMessage.value = 'Merci de renseigner une localisation.'
    return
  }
  if (form.value.type === 'MEETING' && (!form.value.capacite || form.value.capacite < 1)) {
    errorMessage.value = 'La capacité doit être au moins 1 pour une salle de réunion.'
    return
  }

  const payload = {
    nom: form.value.nom,
    type: form.value.type,
    code: form.value.code,
    capacite: form.value.capacite,
    localisation: form.value.localisation,
    description: form.value.description || null,
    statut: form.value.statut,
  }

  isSubmitting.value = true
  try {
    const saved = isEditing.value
      ? await roomService.update(props.room!.id, payload)
      : await roomService.create(payload)
    emit('saved', saved)
    emit('close')
  } catch (e) {
    errorMessage.value = (e as ApiError).message ?? 'Une erreur est survenue. Veuillez réessayer.'
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
            {{ isEditing ? 'Modifier la salle' : 'Ajouter une salle' }}
          </h2>
          <button class="text-muted hover:text-foreground transition-colors" @click="emit('close')">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form class="px-6 py-5 space-y-4" @submit.prevent="handleSubmit">
          <div>
            <label class="text-foreground text-sm font-medium block mb-2">Nom</label>
            <input
              v-model="form.nom"
              type="text"
              placeholder="Salle de réunion A"
              class="w-full bg-background border border-border rounded-lg px-3 py-2.5 text-foreground placeholder:text-muted/60 text-sm focus:outline-none focus:border-primary transition-colors"
            >
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="text-foreground text-sm font-medium block mb-2">Type</label>
              <select
                v-model="form.type"
                class="w-full bg-background border border-border rounded-lg px-3 py-2.5 text-foreground text-sm focus:outline-none focus:border-primary transition-colors"
              >
                <option value="MEETING">Salle de réunion</option>
                <option value="STORAGE">Salle de stockage</option>
              </select>
            </div>
            <div>
              <label class="text-foreground text-sm font-medium block mb-2">Code</label>
              <input
                v-model="form.code"
                type="text"
                placeholder="SAL-A-001"
                class="w-full bg-background border border-border rounded-lg px-3 py-2.5 text-foreground placeholder:text-muted/60 text-sm focus:outline-none focus:border-primary transition-colors"
              >
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div v-if="form.type === 'MEETING'">
              <label class="text-foreground text-sm font-medium block mb-2">Capacité</label>
              <input
                v-model.number="form.capacite"
                type="number"
                min="1"
                class="w-full bg-background border border-border rounded-lg px-3 py-2.5 text-foreground text-sm focus:outline-none focus:border-primary transition-colors"
              >
            </div>
            <div :class="form.type === 'MEETING' ? '' : 'col-span-2'">
              <label class="text-foreground text-sm font-medium block mb-2">Statut</label>
              <select
                v-model="form.statut"
                class="w-full bg-background border border-border rounded-lg px-3 py-2.5 text-foreground text-sm focus:outline-none focus:border-primary transition-colors"
              >
                <option v-for="statut in availableStatuses" :key="statut" :value="statut">
                  {{ statut }}
                </option>
              </select>
            </div>
          </div>

          <div>
            <label class="text-foreground text-sm font-medium block mb-2">Localisation</label>
            <input
              v-model="form.localisation"
              type="text"
              placeholder="Bâtiment A, 2ᵉ étage"
              class="w-full bg-background border border-border rounded-lg px-3 py-2.5 text-foreground placeholder:text-muted/60 text-sm focus:outline-none focus:border-primary transition-colors"
            >
          </div>

          <div>
            <label class="text-foreground text-sm font-medium block mb-2">
              Description <span class="text-muted font-normal">(optionnel)</span>
            </label>
            <textarea
              v-model="form.description"
              rows="2"
              class="w-full bg-background border border-border rounded-lg px-3 py-2.5 text-foreground placeholder:text-muted/60 text-sm focus:outline-none focus:border-primary transition-colors resize-none"
            />
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
              {{ isSubmitting ? 'Enregistrement...' : (isEditing ? 'Enregistrer' : 'Ajouter la salle') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </Teleport>
</template>
