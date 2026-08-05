<script setup lang="ts">
import { Calendar, Clock, Timer, Sun, Moon, Check } from 'lucide-vue-next'
import { ref } from 'vue'
import { useCompanyService } from '~/features/onboarding/services/company.service'
import { WEEK_DAYS } from '~/features/onboarding/type'
import type { ApiError } from '~/composables/useApi'

const emit = defineEmits<{
  back: []
}>()

const companyService = useCompanyService()

const form = ref({
  openingTime: '08:00',
  closingTime: '18:00',
  bufferMinutes: 30,
})

const isSubmitting = ref(false)
const errorMessage = ref('')

const handleSubmit = async () => {
  errorMessage.value = ''

  if (!form.value.openingTime || !form.value.closingTime) {
    errorMessage.value = 'Merci de renseigner les horaires de service.'
    return
  }

  if (form.value.openingTime >= form.value.closingTime) {
    errorMessage.value = "L'heure d'ouverture doit précéder l'heure de fermeture."
    return
  }

  if (form.value.bufferMinutes < 0) {
    errorMessage.value = 'Le délai minimum ne peut pas être négatif.'
    return
  }

  isSubmitting.value = true
  try {
    await companyService.setReservationSettings({
      reservation_buffer: form.value.bufferMinutes,
      service_hours: WEEK_DAYS.map(day => ({
        day_of_week: day,
        is_open: true,
        start_time: form.value.openingTime,
        end_time: form.value.closingTime,
      })),
    })

    await navigateTo('/dashboard')
  } catch (e) {
    const apiError = e as ApiError
    errorMessage.value = apiError.status === 403
      ? "Vous n'avez pas la permission de configurer ce paramètre."
      : apiError.message ?? 'Une erreur est survenue. Veuillez réessayer.'
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <div class="relative w-full min-h-screen flex flex-col items-center justify-center overflow-hidden px-4 py-12 md:py-16">
    <!-- Glow décoratif -->
    <div
      class="pointer-events-none absolute top-0 left-1/2 -translate-x-1/2 w-[320px] h-[240px] md:w-[600px] md:h-[400px] bg-primary/20 rounded-full blur-[80px] md:blur-[120px]"
    />

    <div class="relative z-10 w-full max-w-md">
      <div class="bg-surface border border-border rounded-2xl px-6 py-8 md:px-10 md:py-10">
        <!-- Logo -->
        <div class="flex items-center justify-center gap-2 mb-6">
        <Logo /> 
        </div>

        <!-- Indicateur d'étape -->
        <div class="flex items-center justify-between mb-2">
          <span class="text-primary-light text-xs font-semibold tracking-wide uppercase">
            Étape 2 sur 2
          </span>
          <span class="text-muted text-xs">Paramètres</span>
        </div>
        <div class="w-full h-1 bg-border rounded-full overflow-hidden mb-8">
          <div class="w-full h-full bg-primary rounded-full" />
        </div>

        <!-- Titre + description -->
        <h1 class="text-foreground text-2xl md:text-3xl font-bold leading-snug text-center">
          Paramètres de réservation
        </h1>
        <p class="text-muted text-sm mt-3 leading-relaxed text-center">
          Configurez les paramètres de réservation de votre entreprise. Vous
          pourrez les modifier à tout moment depuis les paramètres de CompanyPilot.
        </p>

        <!-- Formulaire -->
        <form class="mt-8 space-y-6" @submit.prevent="handleSubmit">
          <!-- Horaires de service -->
          <div>
            <div class="flex items-center gap-2 mb-3">
              <Clock class="w-4 h-4 text-primary-light" />
              <h3 class="text-foreground text-sm font-semibold">Horaires de service</h3>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label for="openingTime" class="text-muted text-xs block mb-2">
                  Heure d'ouverture
                </label>
                <div class="relative">
                  <Sun class="w-4 h-4 text-yellow-500 absolute left-3.5 top-1/2 -translate-y-1/2 z-10 pointer-events-none" />
                  <input
                    id="openingTime"
                    v-model="form.openingTime"
                    type="time"
                    class="w-full bg-slate-800 border border-slate-600 rounded-lg pl-10 pr-3 py-2.5 text-foreground text-sm focus:outline-none focus:border-primary transition-colors"
                  >
                </div>
              </div>

              <div>
                <label for="closingTime" class="text-muted text-xs block mb-2">
                  Heure de fermeture
                </label>
                <div class="relative">
                  <Moon class="w-4 h-4 text-primary-light absolute left-3.5 top-1/2 -translate-y-1/2 z-10 pointer-events-none" />
                  <input
                    id="closingTime"
                    v-model="form.closingTime"
                    type="time"
                    filter: brightness(0) invert(1);
                    class="w-full bg-slate-800 border border-slate-600 rounded-lg pl-10 pr-3 py-2.5 text-foreground text-sm focus:outline-none focus:border-primary transition-colors"
                  >
                </div>
              </div>
            </div>

            <p class="text-muted text-xs mt-3">
              Ces horaires définissent la plage pendant laquelle les clients peuvent réserver.
            </p>
          </div>

          <div class="border-t border-border" />

          <!-- Délai minimum entre réservations -->
          <div>
            <div class="flex items-center gap-2 mb-3">
              <Timer class="w-4 h-4 text-primary-light" />
              <h3 class="text-foreground text-sm font-semibold">Délai minimum entre deux réservations</h3>
            </div>

            <div class="flex items-center gap-3">
              <input
                id="bufferMinutes"
                v-model.number="form.bufferMinutes"
                type="number"
                min="0"
                step="5"
                class="w-24 bg-background border border-border rounded-lg px-3 py-2.5 text-foreground text-sm text-center focus:outline-none focus:border-primary transition-colors"
              >
              <span class="text-muted text-sm">minutes</span>
            </div>

            <p class="text-muted text-xs mt-3">
              Durée minimale requise entre la fin d'un créneau et le début du suivant.
            </p>
          </div>

          <p v-if="errorMessage" class="text-red-400 text-sm">
            {{ errorMessage }}
          </p>

          <!-- Boutons -->
          <div class="flex items-center gap-3">
            <button
              type="button"
              class="flex-1 bg-background hover:bg-border text-foreground font-medium py-3 rounded-lg border border-border transition-colors"
              @click="emit('back')"
            >
              Retour
            </button>
            <button
              type="submit"
              :disabled="isSubmitting"
              class="flex-[2] flex items-center justify-center gap-2 bg-primary hover:bg-primary-hover disabled:opacity-60 disabled:cursor-not-allowed text-white font-medium py-3 rounded-lg transition-colors"
            >
              <Check class="w-4 h-4" />
              {{ isSubmitting ? 'Enregistrement...' : 'Terminer la configuration' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <p class="relative z-10 text-center text-muted text-xs mt-8">
      © {{ new Date().getFullYear() }} CompanyPilot. Tous droits réservés.
    </p>
  </div>
</template>