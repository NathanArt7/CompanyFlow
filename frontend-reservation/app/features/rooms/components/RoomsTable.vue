<script setup lang="ts">
import { Users, Pencil, Trash2, ChevronLeft, ChevronRight } from 'lucide-vue-next'
import { computed } from 'vue'
import type { Room, RoomStatus, RoomType } from '../types'
import type { RawRoom } from '../type'

const props = defineProps<{
  rooms: RawRoom[]
  isLoading: boolean
  error: string | null
  page: number
  perPage: number
  total: number
  lastPage: number
}>()

const emit = defineEmits<{
  'update:page': [number]
  'update:perPage': [number]
  edit: [RawRoom]
  delete: [RawRoom]
}>()

const typeMap: Record<string, RoomType> = {
  MEETING: 'reunion',
  STORAGE: 'stockage',
}

const statusMap: Record<string, RoomStatus> = {
  Disponible: 'available',
  Occupée: 'occupied',
  'En maintenance': 'maintenance',
  'Hors service': 'out_of_service',
}

function mapRoom(r: RawRoom): Room & { raw: RawRoom } {
  return {
    id: String(r.id),
    name: r.nom,
    code: r.code,
    type: typeMap[r.type.value] ?? 'reunion',
    capacity: r.capacite,
    location: r.localisation,
    description: r.description,
    status: statusMap[r.statut.value] ?? 'available',
    raw: r,
  }
}

const rows = computed(() => props.rooms.map(mapRoom))

const pageSize = computed({
  get: () => String(props.perPage),
  set: (value: string) => emit('update:perPage', Number(value)),
})

const rangeStart = computed(() => props.total === 0 ? 0 : (props.page - 1) * props.perPage + 1)
const rangeEnd = computed(() => Math.min(props.page * props.perPage, props.total))
</script>

<template>
  <div class="bg-surface border border-border rounded-xl overflow-hidden">
    <SkeletonTable v-if="isLoading" :rows="perPage > 8 ? 8 : perPage" :columns="7" />
    <p v-else-if="error" class="text-red-400 text-xs p-5">
      {{ error }}
    </p>
    <p v-else-if="rows.length === 0" class="text-muted text-xs p-5">
      Aucune salle trouvée.
    </p>

    <template v-else>
      <!-- Tableau desktop -->
      <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="text-left text-muted text-xs border-b border-border">
              <th class="font-medium py-3 pl-5 pr-4">Salle</th>
              <th class="font-medium py-3 pr-4">Type</th>
              <th class="font-medium py-3 pr-4">Capacité</th>
              <th class="font-medium py-3 pr-4">Localisation</th>
              <th class="font-medium py-3 pr-4">Description</th>
              <th class="font-medium py-3 pr-4">Statut</th>
              <th class="font-medium py-3 pr-5">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="room in rows"
              :key="room.id"
              class="border-b border-border last:border-0 hover:bg-background/40 transition-colors"
            >
              <td class="py-4 pl-5 pr-4">
                <p class="text-foreground font-medium">{{ room.name }}</p>
                <p class="text-muted text-xs">Code: {{ room.code }}</p>
              </td>
              <td class="py-4 pr-4">
                <RoomTypeBadge :type="room.type" />
              </td>
              <td class="py-4 pr-4">
                <div v-if="room.capacity" class="flex items-center gap-1.5 text-foreground">
                  <Users class="w-4 h-4 text-muted" />
                  {{ room.capacity }} places
                </div>
                <span v-else class="text-muted">—</span>
              </td>
              <td class="py-4 pr-4 text-foreground">{{ room.location }}</td>
              <td class="py-4 pr-4 text-muted max-w-xs whitespace-normal break-words">{{ room.description || '—' }}</td>
              <td class="py-4 pr-4">
                <RoomStatusBadge :status="room.status" />
              </td>
              <td class="py-4 pr-5">
                <div class="flex items-center gap-2">
                  <button
                    class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-foreground hover:bg-border/50 transition-colors"
                    title="Modifier"
                    @click="emit('edit', room.raw)"
                  >
                    <Pencil class="w-3.5 h-3.5" />
                  </button>
                  <button
                    class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-red-400 hover:border-red-400/40 transition-colors"
                    title="Supprimer"
                    @click="emit('delete', room.raw)"
                  >
                    <Trash2 class="w-3.5 h-3.5" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Cards mobile -->
      <div class="md:hidden divide-y divide-border">
        <div v-for="room in rows" :key="room.id" class="p-4">
          <div class="flex items-center justify-between mb-2">
            <div>
              <p class="text-foreground font-medium">{{ room.name }}</p>
              <p class="text-muted text-xs">Code: {{ room.code }}</p>
            </div>
            <RoomTypeBadge :type="room.type" />
          </div>
          <div class="flex items-center justify-between text-sm mb-2">
            <span class="text-muted">{{ room.location }}</span>
            <span v-if="room.capacity" class="flex items-center gap-1 text-foreground">
              <Users class="w-3.5 h-3.5 text-muted" />
              {{ room.capacity }} places
            </span>
          </div>
          <p v-if="room.description" class="text-muted text-xs mb-2 whitespace-normal break-words">{{ room.description }}</p>
          <div class="flex items-center justify-between">
            <RoomStatusBadge :status="room.status" />
            <div class="flex items-center gap-2">
              <button
                class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-foreground hover:bg-border/50 transition-colors"
                title="Modifier"
                @click="emit('edit', room.raw)"
              >
                <Pencil class="w-3.5 h-3.5" />
              </button>
              <button
                class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-red-400 hover:border-red-400/40 transition-colors"
                title="Supprimer"
                @click="emit('delete', room.raw)"
              >
                <Trash2 class="w-3.5 h-3.5" />
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-t border-border">
        <p class="text-muted text-xs">
          Affichage de {{ rangeStart }} à {{ rangeEnd }} sur {{ total }} salles
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
