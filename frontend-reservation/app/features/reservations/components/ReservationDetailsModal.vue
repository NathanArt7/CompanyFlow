<script setup lang="ts">
import { X } from 'lucide-vue-next'
import { computed } from 'vue'
import type { RawReservation, ReservationStatus } from '../type'

const props = defineProps<{
  isOpen: boolean
  reservation: RawReservation | null
}>()

const emit = defineEmits<{
  close: []
}>()

const cancellationReasonLabels: Record<string, string> = {
  USER_REQUEST: 'Annulée par le créateur',
  ADMIN_DECISION: "Décision d'un administrateur",
  ROOM_DELETED: 'Salle supprimée',
  USER_DELETED: 'Utilisateur supprimé',
}

const formattedDate = computed(() => {
  if (!props.reservation) return ''
  return new Date(props.reservation.date_reservation.slice(0, 10)).toLocaleDateString('fr-FR', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  })
})

// Le statut affiché doit refléter le temps écoulé (en cours/passée), pas uniquement
// la valeur "statut" brute en base qui ne distingue que confirmée/annulée.
const status = computed<ReservationStatus>(() => {
  if (!props.reservation) return 'confirmed'
  if (props.reservation.statut === 'ANNULEE') return 'cancelled'

  const datePart = props.reservation.date_reservation.slice(0, 10)
  const start = new Date(`${datePart}T${props.reservation.heure_debut}`)
  const end = new Date(`${datePart}T${props.reservation.heure_fin}`)
  const now = new Date()

  if (now < start) return 'confirmed'
  if (now < end) return 'ongoing'
  return 'past'
})
</script>

<template>
  <Teleport to="body">
    <div
      v-if="isOpen && reservation"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60"
      @click.self="emit('close')"
    >
      <div class="bg-surface border border-border rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-border">
          <h2 class="text-foreground text-lg font-semibold">Détails de la réservation</h2>
          <button class="text-muted hover:text-foreground transition-colors" @click="emit('close')">
            <X class="w-5 h-5" />
          </button>
        </div>

        <div class="px-6 py-5 space-y-4">
          <div>
            <p class="text-muted text-xs mb-1">Motif</p>
            <p class="text-foreground font-medium">{{ reservation.motif }}</p>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-muted text-xs mb-1">Salle</p>
              <p :class="reservation.room?.deleted_at ? 'text-red-400' : 'text-foreground'">{{ reservation.room?.nom ?? '—' }}</p>
              <p v-if="reservation.room?.deleted_at" class="text-red-400 text-xs">(Salle supprimée)</p>
              <p v-else-if="reservation.room?.localisation" class="text-muted text-xs">{{ reservation.room.localisation }}</p>
            </div>
            <div>
              <p class="text-muted text-xs mb-1">Participants</p>
              <p class="text-foreground">{{ reservation.nombre_participants }}</p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-muted text-xs mb-1">Date</p>
              <p class="text-foreground capitalize">{{ formattedDate }}</p>
            </div>
            <div>
              <p class="text-muted text-xs mb-1">Horaire</p>
              <p class="text-foreground">{{ reservation.heure_debut.slice(0, 5) }} - {{ reservation.heure_fin.slice(0, 5) }}</p>
            </div>
          </div>

          <div>
            <p class="text-muted text-xs mb-1">Organisateur</p>
            <p :class="reservation.user?.deleted_at ? 'text-red-400' : 'text-foreground'">
              {{ reservation.user ? `${reservation.user.prenom} ${reservation.user.nom}` : '—' }}
            </p>
            <p v-if="reservation.user?.deleted_at" class="text-red-400 text-xs">(Utilisateur supprimé)</p>
            <p v-else-if="reservation.user" class="text-muted text-xs">{{ reservation.user.email }}</p>
          </div>

          <div>
            <p class="text-muted text-xs mb-1">Équipements</p>
            <p v-if="reservation.equipments.length === 0" class="text-muted text-sm">
              Aucun équipement associé.
            </p>
            <p v-else class="text-foreground text-sm">
              {{ reservation.equipments.map(e => e.nom).join(', ') }}
            </p>
          </div>

          <div>
            <p class="text-muted text-xs mb-1">Statut</p>
            <ReservationStatusBadge :status="status" />
          </div>

          <template v-if="reservation.statut === 'ANNULEE'">
            <div class="border-t border-border pt-4">
              <p class="text-muted text-xs mb-1">Annulée par</p>
              <p :class="reservation.cancelled_by?.deleted_at ? 'text-red-400' : 'text-foreground'">
                {{ reservation.cancelled_by ? `${reservation.cancelled_by.prenom} ${reservation.cancelled_by.nom}` : '—' }}
              </p>
              <p v-if="reservation.cancelled_by?.deleted_at" class="text-red-400 text-xs">(Utilisateur supprimé)</p>
            </div>
            <div v-if="reservation.cancellation_reason">
              <p class="text-muted text-xs mb-1">Motif d'annulation</p>
              <p class="text-foreground">{{ cancellationReasonLabels[reservation.cancellation_reason] ?? reservation.cancellation_reason }}</p>
            </div>
          </template>
        </div>
      </div>
    </div>
  </Teleport>
</template>
