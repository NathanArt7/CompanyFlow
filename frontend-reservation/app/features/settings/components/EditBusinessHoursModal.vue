<script setup lang="ts">
import { Loader2, X } from 'lucide-vue-next'
import { ref, watch } from 'vue'
import type { ApiError } from '~/composables/useApi'
import type { DayOfWeek, RawReservationSetting } from '../type'
import { WEEK_DAYS } from '../type'
import { useReservationSettingService } from '../services/reservation-setting.service'

const props = defineProps<{
  isOpen: boolean
  setting: RawReservationSetting | null
}>()

const emit = defineEmits<{
  close: []
  saved: [RawReservationSetting]
}>()

const reservationSettingService = useReservationSettingService()
const toast = useToast()

const dayLabels: Record<DayOfWeek, string> = {
  MONDAY: 'Lundi',
  TUESDAY: 'Mardi',
  WEDNESDAY: 'Mercredi',
  THURSDAY: 'Jeudi',
  FRIDAY: 'Vendredi',
  SATURDAY: 'Samedi',
  SUNDAY: 'Dimanche',
}

interface DayForm {
  day_of_week: DayOfWeek
  is_open: boolean
  start_time: string
  end_time: string
}

const reservationBuffer = ref(0)
const days = ref<DayForm[]>([])

const isSubmitting = ref(false)
const errorMessage = ref('')

function resetForm() {
  reservationBuffer.value = props.setting?.reservation_buffer ?? 0

  days.value = WEEK_DAYS.map((day) => {
    const existing = props.setting?.service_hours.find(h => h.day_of_week === day)
    return {
      day_of_week: day,
      is_open: existing?.is_open ?? true,
      start_time: existing?.start_time?.slice(0, 5) ?? '08:00',
      end_time: existing?.end_time?.slice(0, 5) ?? '18:00',
    }
  })

  errorMessage.value = ''
}

watch(() => props.isOpen, (open) => {
  if (open) resetForm()
})

async function handleSubmit() {
  errorMessage.value = ''

  for (const day of days.value) {
    if (day.is_open && day.start_time >= day.end_time) {
      errorMessage.value = `${dayLabels[day.day_of_week]} : l'heure de fermeture doit être postérieure à l'heure d'ouverture.`
      return
    }
  }

  isSubmitting.value = true
  try {
    const saved = await reservationSettingService.update({
      reservation_buffer: reservationBuffer.value,
      service_hours: days.value.map(day => ({
        day_of_week: day.day_of_week,
        is_open: day.is_open,
        start_time: day.is_open ? day.start_time : null,
        end_time: day.is_open ? day.end_time : null,
      })),
    })
    emit('saved', saved)
    toast.success('Horaires modifiés avec succès.')
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
          <h2 class="text-foreground text-lg font-semibold">Horaires de service</h2>
          <button class="text-muted hover:text-foreground transition-colors" @click="emit('close')">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form class="px-6 py-5 space-y-4" @submit.prevent="handleSubmit">
          <div v-for="day in days" :key="day.day_of_week" class="flex items-center gap-3">
            <label class="flex items-center gap-2 w-28 shrink-0 text-sm text-foreground">
              <input v-model="day.is_open" type="checkbox" class="accent-primary">
              {{ dayLabels[day.day_of_week] }}
            </label>

            <template v-if="day.is_open">
              <input
                v-model="day.start_time"
                type="time"
                class="flex-1 bg-background border border-border rounded-lg px-3 py-2 text-foreground text-sm focus:outline-none focus:border-primary transition-colors"
              >
              <span class="text-muted text-sm">à</span>
              <input
                v-model="day.end_time"
                type="time"
                class="flex-1 bg-background border border-border rounded-lg px-3 py-2 text-foreground text-sm focus:outline-none focus:border-primary transition-colors"
              >
            </template>
            <span v-else class="flex-1 text-muted text-sm">Fermé</span>
          </div>

          <div class="border-t border-border pt-4">
            <label class="text-foreground text-sm font-medium block mb-2">
              Délai minimum entre deux réservations
            </label>
            <div class="flex items-center gap-3">
              <input
                v-model.number="reservationBuffer"
                type="number"
                min="0"
                step="5"
                class="w-24 bg-background border border-border rounded-lg px-3 py-2.5 text-foreground text-sm text-center focus:outline-none focus:border-primary transition-colors"
              >
              <span class="text-muted text-sm">minutes</span>
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
              {{ isSubmitting ? 'Enregistrement...' : 'Enregistrer' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </Teleport>
</template>
