<script setup lang="ts">
import { X, Loader2 } from 'lucide-vue-next'
import { ref, computed, watch, onMounted } from 'vue'
import type { RawEquipment } from '~/features/equipments/type'
import { useEquipmentService } from '~/features/equipments/services/equipment.service'
import { useTicketService } from '../services/ticket.service'
import type { RawTicket } from '../type'

const props = defineProps<{
  isOpen: boolean
}>()

const emit = defineEmits<{
  close: []
  saved: [RawTicket]
}>()

const authStore = useAuthStore()
const equipmentService = useEquipmentService()
const ticketService = useTicketService()

const equipments = ref<RawEquipment[]>([])
const isLoadingEquipments = ref(false)
const equipmentsError = ref(false)

// Mêmes règles d'éligibilité que côté backend (TicketService::ensureEquipmentIsEligibleForTicket) :
// Super Admin/Admin voient tout, Super Employé voit en plus tout l'empruntable, et le
// non-empruntable n'est proposé que s'il est assigné à l'utilisateur ou non assigné.
const eligibleEquipments = computed(() => {
  const role = authStore.user?.role
  const userId = authStore.user?.id

  if (role === 'Super_Administrateur' || role === 'Administrateur') {
    return equipments.value
  }

  return equipments.value.filter((equipment) => {
    if (role === 'Super_Employe' && equipment.usage_type === 'EMPRUNTABLE') return true

    return (
      equipment.usage_type === 'NON_EMPRUNTABLE'
      && (equipment.assigned_user === null || equipment.assigned_user?.id === userId)
    )
  })
})

const eligibleEmpruntable = computed(() => eligibleEquipments.value.filter(e => e.usage_type === 'EMPRUNTABLE'))
const eligibleNonEmpruntable = computed(() => eligibleEquipments.value.filter(e => e.usage_type === 'NON_EMPRUNTABLE'))

// Deux sélecteurs distincts (empruntable / non empruntable), mais un seul matériel
// au final : choisir dans l'un vide automatiquement l'autre.
const selectedEmpruntableId = ref<number | null>(null)
const selectedNonEmpruntableId = ref<number | null>(null)

function onSelectEmpruntable() {
  selectedNonEmpruntableId.value = null
}

function onSelectNonEmpruntable() {
  selectedEmpruntableId.value = null
}

const form = ref({
  description: '',
})

const isSubmitting = ref(false)
const errorMessage = ref('')

function resetForm() {
  selectedEmpruntableId.value = null
  selectedNonEmpruntableId.value = null
  form.value = { description: '' }
  errorMessage.value = ''
}

watch(() => props.isOpen, (open) => {
  if (open) resetForm()
})

async function loadEquipments() {
  isLoadingEquipments.value = true
  equipmentsError.value = false
  try {
    equipments.value = (await equipmentService.paginate({ per_page: 100, sort: 'nom', direction: 'asc' })).data
  } catch {
    equipmentsError.value = true
  } finally {
    isLoadingEquipments.value = false
  }
}

onMounted(loadEquipments)

async function handleSubmit() {
  errorMessage.value = ''

  const equipmentId = selectedEmpruntableId.value ?? selectedNonEmpruntableId.value

  if (!equipmentId) {
    errorMessage.value = 'Merci de sélectionner un matériel.'
    return
  }
  if (!form.value.description.trim()) {
    errorMessage.value = 'Merci de décrire le problème rencontré.'
    return
  }

  isSubmitting.value = true
  try {
    const saved = await ticketService.create({
      equipment_id: equipmentId,
      description: form.value.description.trim(),
    })
    emit('saved', saved)
    emit('close')
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
      <div class="bg-surface border border-border rounded-2xl w-full max-w-lg">
        <div class="flex items-center justify-between px-6 py-4 border-b border-border">
          <h2 class="text-foreground text-lg font-semibold">Nouveau ticket</h2>
          <button class="text-muted hover:text-foreground transition-colors" @click="emit('close')">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form class="px-6 py-5 space-y-4" @submit.prevent="handleSubmit">
          <div>
            <label class="text-foreground text-sm font-medium block mb-2">Matériel empruntable</label>
            <select
              v-model="selectedEmpruntableId"
              :disabled="equipmentsError || isLoadingEquipments"
              class="w-full bg-background border border-border rounded-lg px-3 py-2.5 text-foreground text-sm focus:outline-none focus:border-primary transition-colors disabled:opacity-60"
              @change="onSelectEmpruntable"
            >
              <option :value="null">Sélectionner un matériel empruntable</option>
              <option v-for="equipment in eligibleEmpruntable" :key="equipment.id" :value="equipment.id">
                {{ equipment.nom }} ({{ equipment.code }})
              </option>
            </select>
            <p v-if="!isLoadingEquipments && !equipmentsError && eligibleEmpruntable.length === 0" class="text-muted text-xs mt-1">
              Aucun matériel empruntable disponible.
            </p>
          </div>

          <div>
            <label class="text-foreground text-sm font-medium block mb-2">Matériel non empruntable</label>
            <select
              v-model="selectedNonEmpruntableId"
              :disabled="equipmentsError || isLoadingEquipments"
              class="w-full bg-background border border-border rounded-lg px-3 py-2.5 text-foreground text-sm focus:outline-none focus:border-primary transition-colors disabled:opacity-60"
              @change="onSelectNonEmpruntable"
            >
              <option :value="null">Sélectionner un matériel non empruntable</option>
              <option v-for="equipment in eligibleNonEmpruntable" :key="equipment.id" :value="equipment.id">
                {{ equipment.nom }} ({{ equipment.code }})
              </option>
            </select>
            <p v-if="equipmentsError" class="text-muted text-xs mt-1">
              Impossible de charger la liste des matériels.
            </p>
            <p v-else-if="!isLoadingEquipments && eligibleNonEmpruntable.length === 0" class="text-muted text-xs mt-1">
              Aucun matériel non empruntable disponible.
            </p>
          </div>

          <div>
            <label class="text-foreground text-sm font-medium block mb-2">Description du problème</label>
            <textarea
              v-model="form.description"
              rows="4"
              placeholder="Décrivez le problème rencontré..."
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
              {{ isSubmitting ? 'Création...' : 'Créer le ticket' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </Teleport>
</template>
