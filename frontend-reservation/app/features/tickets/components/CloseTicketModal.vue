<script setup lang="ts">
import { X, Loader2, CheckCircle2, XCircle } from 'lucide-vue-next'
import { ref, watch } from 'vue'
import { useTicketService } from '../services/ticket.service'
import type { RawTicket } from '../type'

const props = defineProps<{
  isOpen: boolean
  ticket: RawTicket | null
}>()

const emit = defineEmits<{
  close: []
  saved: [RawTicket]
}>()

const ticketService = useTicketService()
const toast = useToast()

const selectedState = ref<'FONCTIONNEL' | 'HORS_SERVICE' | null>(null)
const isSubmitting = ref(false)
const errorMessage = ref('')

watch(() => props.isOpen, (open) => {
  if (open) {
    selectedState.value = null
    errorMessage.value = ''
  }
})

async function handleSubmit() {
  if (!props.ticket) return

  if (!selectedState.value) {
    errorMessage.value = "Merci de préciser l'état du matériel."
    return
  }

  isSubmitting.value = true
  errorMessage.value = ''
  try {
    const saved = await ticketService.close(props.ticket.id, selectedState.value)
    emit('saved', saved)
    emit('close')
    toast.success('Ticket fermé avec succès.')
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
      v-if="isOpen && ticket"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60"
      @click.self="emit('close')"
    >
      <div class="bg-surface border border-border rounded-2xl w-full max-w-sm">
        <div class="flex items-center justify-between px-6 py-4 border-b border-border">
          <h2 class="text-foreground text-lg font-semibold">Fermer le ticket</h2>
          <button class="text-muted hover:text-foreground transition-colors" @click="emit('close')">
            <X class="w-5 h-5" />
          </button>
        </div>

        <div class="px-6 py-5 space-y-4">
          <p class="text-muted text-sm">
            Quel est l'état de « {{ ticket.equipment?.nom }} » après intervention ?
          </p>

          <div class="grid grid-cols-2 gap-3">
            <button
              type="button"
              class="flex flex-col items-center gap-2 border rounded-lg py-4 transition-colors"
              :class="selectedState === 'FONCTIONNEL'
                ? 'border-green-500 bg-green-500/10 text-green-500'
                : 'border-border text-muted hover:border-green-500/40'"
              @click="selectedState = 'FONCTIONNEL'"
            >
              <CheckCircle2 class="w-6 h-6" />
              <span class="text-sm font-medium">Fonctionnel</span>
            </button>
            <button
              type="button"
              class="flex flex-col items-center gap-2 border rounded-lg py-4 transition-colors"
              :class="selectedState === 'HORS_SERVICE'
                ? 'border-red-400 bg-red-500/10 text-red-400'
                : 'border-border text-muted hover:border-red-400/40'"
              @click="selectedState = 'HORS_SERVICE'"
            >
              <XCircle class="w-6 h-6" />
              <span class="text-sm font-medium">Hors service</span>
            </button>
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
              type="button"
              :disabled="isSubmitting"
              class="flex-1 flex items-center justify-center gap-2 bg-primary hover:bg-primary-hover disabled:opacity-60 disabled:cursor-not-allowed text-white font-medium py-2.5 rounded-lg transition-colors"
              @click="handleSubmit"
            >
              <Loader2 v-if="isSubmitting" class="w-4 h-4 animate-spin" />
              {{ isSubmitting ? 'Fermeture...' : 'Fermer le ticket' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>
