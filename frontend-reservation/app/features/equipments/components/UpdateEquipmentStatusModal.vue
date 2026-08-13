<script setup lang="ts">
import { X, Loader2 } from 'lucide-vue-next'
import { ref, computed, watch } from 'vue'
import { useEquipmentService } from '../services/equipment.service'
import type { RawEquipment } from '../type'

const props = defineProps<{
  isOpen: boolean
  equipment: RawEquipment | null
}>()

const emit = defineEmits<{
  close: []
  saved: [RawEquipment]
}>()

const equipmentService = useEquipmentService()
const toast = useToast()

const empruntableStates: Array<{ value: string, label: string }> = [
  { value: 'DISPONIBLE', label: 'Disponible' },
  { value: 'OCCUPE', label: 'Occupé' },
  { value: 'EN_PANNE', label: 'En panne' },
  { value: 'EN_MAINTENANCE', label: 'En maintenance' },
  { value: 'HORS_SERVICE', label: 'Hors service' },
]

const nonEmpruntableStates: Array<{ value: string, label: string }> = [
  { value: 'FONCTIONNEL', label: 'Fonctionnel' },
  { value: 'EN_PANNE', label: 'En panne' },
  { value: 'EN_MAINTENANCE', label: 'En maintenance' },
  { value: 'HORS_SERVICE', label: 'Hors service' },
]

const availableStates = computed(() =>
  props.equipment?.usage_type === 'EMPRUNTABLE' ? empruntableStates : nonEmpruntableStates,
)

const selectedState = ref<string | null>(null)
const isSubmitting = ref(false)
const errorMessage = ref('')

watch(() => props.isOpen, (open) => {
  if (open) {
    selectedState.value = props.equipment?.etat ?? null
    errorMessage.value = ''
  }
})

async function handleSubmit() {
  if (!props.equipment) return

  if (!selectedState.value) {
    errorMessage.value = "Merci de sélectionner un état."
    return
  }

  isSubmitting.value = true
  errorMessage.value = ''
  try {
    const saved = await equipmentService.updateStatus(props.equipment.id, selectedState.value)
    emit('saved', saved)
    emit('close')
    toast.success('État modifié avec succès.')
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
      v-if="isOpen && equipment"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60"
      @click.self="emit('close')"
    >
      <div class="bg-surface border border-border rounded-2xl w-full max-w-sm">
        <div class="flex items-center justify-between px-6 py-4 border-b border-border">
          <h2 class="text-foreground text-lg font-semibold">Modifier l'état</h2>
          <button class="text-muted hover:text-foreground transition-colors" @click="emit('close')">
            <X class="w-5 h-5" />
          </button>
        </div>

        <div class="px-6 py-5 space-y-4">
          <p class="text-muted text-sm">
            État de « {{ equipment.nom }} »
          </p>

          <select
            v-model="selectedState"
            class="w-full bg-background border border-border rounded-lg px-3 py-2.5 text-foreground text-sm focus:outline-none focus:border-primary transition-colors"
          >
            <option v-for="state in availableStates" :key="state.value" :value="state.value">
              {{ state.label }}
            </option>
          </select>

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
              type="button"
              :disabled="isSubmitting"
              class="flex-1 flex items-center justify-center gap-2 bg-primary hover:bg-primary-hover disabled:opacity-60 disabled:cursor-not-allowed text-white font-medium py-2.5 rounded-lg transition-colors"
              @click="handleSubmit"
            >
              <Loader2 v-if="isSubmitting" class="w-4 h-4 animate-spin" />
              {{ isSubmitting ? 'Enregistrement...' : 'Enregistrer' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>
