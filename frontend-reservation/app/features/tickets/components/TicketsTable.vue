<script setup lang="ts">
import { ChevronLeft, ChevronRight, Wrench, CheckCircle2 } from 'lucide-vue-next'
import { computed } from 'vue'
import type { RawTicket } from '../type'

const props = defineProps<{
  tickets: RawTicket[]
  isLoading: boolean
  error: string | null
  page: number
  perPage: number
  total: number
  lastPage: number
  showCreator?: boolean
  showActions?: boolean
}>()

const emit = defineEmits<{
  'update:page': [number]
  'update:perPage': [number]
  accept: [RawTicket]
  'request-close': [RawTicket]
}>()

function formatDate(value: string) {
  return new Date(value).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}

// Salle de stockage pour le matériel empruntable, localisation saisie à la
// création pour le non-empruntable.
function locationFor(ticket: RawTicket): string {
  if (!ticket.equipment) return '—'
  return ticket.equipment.usage_type === 'EMPRUNTABLE'
    ? ticket.equipment.storage_room?.nom ?? '—'
    : ticket.equipment.localisation ?? '—'
}

// "etat" recouvre deux enums différentes selon usage_type (empruntable vs non
// empruntable) mais partage certaines valeurs (EN_PANNE, EN_MAINTENANCE,
// HORS_SERVICE) : une seule table de libellés couvre donc les deux cas.
const etatConfig: Record<string, { label: string, class: string }> = {
  DISPONIBLE: { label: 'Disponible', class: 'bg-green-500/10 text-green-500 border-green-500/20' },
  FONCTIONNEL: { label: 'Fonctionnel', class: 'bg-green-500/10 text-green-500 border-green-500/20' },
  OCCUPE: { label: 'Occupé', class: 'bg-blue-500/10 text-blue-400 border-blue-500/20' },
  EN_MAINTENANCE: { label: 'En maintenance', class: 'bg-orange-500/10 text-orange-500 border-orange-500/20' },
  EN_PANNE: { label: 'En panne', class: 'bg-red-500/10 text-red-400 border-red-500/20' },
  HORS_SERVICE: { label: 'Hors service', class: 'bg-gray-500/10 text-muted border-gray-500/20' },
}

function equipmentStateConfig(ticket: RawTicket) {
  const etat = ticket.equipment?.etat
  return etat && etatConfig[etat] ? etatConfig[etat] : { label: '—', class: 'bg-gray-500/10 text-muted border-gray-500/20' }
}

// "Type" = type d'usage du matériel (empruntable / non empruntable), pas sa catégorie.
function usageTypeLabel(ticket: RawTicket): string {
  if (!ticket.equipment) return '—'
  return ticket.equipment.usage_type === 'EMPRUNTABLE' ? 'Empruntable' : 'Non empruntable'
}

const pageSize = computed({
  get: () => String(props.perPage),
  set: (value: string) => emit('update:perPage', Number(value)),
})

const rangeStart = computed(() => props.total === 0 ? 0 : (props.page - 1) * props.perPage + 1)
const rangeEnd = computed(() => Math.min(props.page * props.perPage, props.total))

const columnCount = computed(() => 5 + (props.showCreator ? 3 : 0) + (props.showActions ? 1 : 0))
</script>

<template>
  <div class="bg-surface border border-border rounded-xl overflow-hidden">
    <SkeletonTable v-if="isLoading" :rows="perPage > 8 ? 8 : perPage" :columns="columnCount" />
    <p v-else-if="error" class="text-red-400 text-xs p-5">
      {{ error }}
    </p>
    <p v-else-if="tickets.length === 0" class="text-muted text-xs p-5">
      Aucun ticket pour le moment.
    </p>

    <template v-else>
      <!-- Tableau desktop -->
      <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="text-left text-muted text-xs border-b border-border">
              <th class="font-medium py-3 pl-5 pr-4">Matériel</th>
              <th class="font-medium py-3 pr-4">État actuel du matériel</th>
              <th v-if="showCreator" class="font-medium py-3 pr-4">Type</th>
              <th v-if="showCreator" class="font-medium py-3 pr-4">Localisation</th>
              <th class="font-medium py-3 pr-4">Description</th>
              <th v-if="showCreator" class="font-medium py-3 pr-4">Créé par</th>
              <th class="font-medium py-3 pr-4">Statut</th>
              <th class="font-medium py-3 pr-4">Créé le</th>
              <th v-if="showActions" class="font-medium py-3 pr-5">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="ticket in tickets"
              :key="ticket.id"
              class="border-b border-border last:border-0 hover:bg-background/40 transition-colors"
            >
              <td class="py-4 pl-5 pr-4">
                <p class="text-foreground font-medium">{{ ticket.equipment?.nom ?? '—' }}</p>
                <p class="text-muted text-xs">{{ ticket.equipment?.code }}</p>
              </td>
              <td class="py-4 pr-4">
                <span
                  class="inline-flex items-center whitespace-nowrap text-xs font-medium px-2.5 py-1 rounded-full border"
                  :class="equipmentStateConfig(ticket).class"
                >
                  {{ equipmentStateConfig(ticket).label }}
                </span>
              </td>
              <td v-if="showCreator" class="py-4 pr-4 text-foreground whitespace-nowrap">
                {{ usageTypeLabel(ticket) }}
              </td>
              <td v-if="showCreator" class="py-4 pr-4 text-foreground whitespace-nowrap">
                {{ locationFor(ticket) }}
              </td>
              <td class="py-4 pr-4 text-foreground max-w-xs whitespace-normal break-words">{{ ticket.description }}</td>
              <td v-if="showCreator" class="py-4 pr-4 text-foreground whitespace-nowrap">
                {{ ticket.user ? `${ticket.user.prenom} ${ticket.user.nom}` : '—' }}
              </td>
              <td class="py-4 pr-4">
                <TicketStatusBadge :status="ticket.statut" />
              </td>
              <td class="py-4 pr-4 text-muted whitespace-nowrap">{{ formatDate(ticket.created_at) }}</td>
              <td v-if="showActions" class="py-4 pr-5">
                <button
                  v-if="ticket.statut === 'OUVERT'"
                  class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-primary-light hover:border-primary/40 transition-colors"
                  title="Prendre en charge"
                  @click="emit('accept', ticket)"
                >
                  <Wrench class="w-3.5 h-3.5" />
                </button>
                <button
                  v-else-if="ticket.statut === 'EN_COURS'"
                  class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-green-500 hover:border-green-500/40 transition-colors"
                  title="Fermer le ticket"
                  @click="emit('request-close', ticket)"
                >
                  <CheckCircle2 class="w-3.5 h-3.5" />
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Cards mobile -->
      <div class="md:hidden divide-y divide-border">
        <div v-for="ticket in tickets" :key="ticket.id" class="p-4">
          <div class="flex items-center justify-between mb-2">
            <p class="text-foreground font-medium">{{ ticket.equipment?.nom ?? '—' }}</p>
            <TicketStatusBadge :status="ticket.statut" />
          </div>
          <div class="flex items-center gap-2 mb-2">
            <p class="text-muted text-xs">{{ ticket.equipment?.code }}</p>
            <span
              class="inline-flex items-center text-xs font-medium px-2.5 py-1 rounded-full border"
              :class="equipmentStateConfig(ticket).class"
            >
              {{ equipmentStateConfig(ticket).label }}
            </span>
          </div>
          <p v-if="showCreator" class="text-muted text-xs mb-1">
            {{ usageTypeLabel(ticket) }} · {{ locationFor(ticket) }}
          </p>
          <p class="text-foreground text-sm mb-2 break-words">{{ ticket.description }}</p>
          <p v-if="showCreator" class="text-muted text-xs">
            Par {{ ticket.user ? `${ticket.user.prenom} ${ticket.user.nom}` : '—' }}
          </p>
          <div class="flex items-center justify-between mt-2">
            <p class="text-muted text-xs">{{ formatDate(ticket.created_at) }}</p>
            <button
              v-if="showActions && ticket.statut === 'OUVERT'"
              class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-primary-light hover:border-primary/40 transition-colors"
              title="Prendre en charge"
              @click="emit('accept', ticket)"
            >
              <Wrench class="w-3.5 h-3.5" />
            </button>
            <button
              v-else-if="showActions && ticket.statut === 'EN_COURS'"
              class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-green-500 hover:border-green-500/40 transition-colors"
              title="Fermer le ticket"
              @click="emit('request-close', ticket)"
            >
              <CheckCircle2 class="w-3.5 h-3.5" />
            </button>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-t border-border">
        <p class="text-muted text-xs">
          Affichage de {{ rangeStart }} à {{ rangeEnd }} sur {{ total }} tickets
        </p>

        <div class="flex items-center gap-2">
          <button
            class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-foreground hover:bg-border/50 transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
            :disabled="page === 1"
            @click="emit('update:page', page - 1)"
          >
            <ChevronLeft class="w-4 h-4" />
          </button>

          <button
            v-for="p in lastPage"
            :key="p"
            class="w-8 h-8 flex items-center justify-center rounded-lg text-sm font-medium transition-colors"
            :class="page === p ? 'bg-primary text-white' : 'text-muted hover:text-foreground hover:bg-border/50'"
            @click="emit('update:page', p)"
          >
            {{ p }}
          </button>

          <button
            class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-foreground hover:bg-border/50 transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
            :disabled="page === lastPage"
            @click="emit('update:page', page + 1)"
          >
            <ChevronRight class="w-4 h-4" />
          </button>

          <select
            v-model="pageSize"
            class="bg-background border border-border rounded-lg px-3 py-1.5 text-sm text-foreground focus:outline-none focus:border-primary transition-colors ml-1"
          >
            <option value="10">10 par page</option>
            <option value="20">20 par page</option>
            <option value="50">50 par page</option>
          </select>
        </div>
      </div>
    </template>
  </div>
</template>
