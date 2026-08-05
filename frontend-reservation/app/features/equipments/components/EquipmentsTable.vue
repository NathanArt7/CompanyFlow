<script setup lang="ts">
import { Pencil, Trash2, ChevronLeft, ChevronRight, Wrench } from 'lucide-vue-next'
import { computed } from 'vue'
import type { Equipment, EquipmentState, EquipmentUsageType } from '../types'
import type { RawEquipment } from '../type'

const props = defineProps<{
  equipments: RawEquipment[]
  isLoading: boolean
  error: string | null
  page: number
  perPage: number
  total: number
  lastPage: number
  canFullEdit?: boolean
  canEditState?: boolean
  canDelete?: boolean
}>()

const emit = defineEmits<{
  'update:page': [number]
  'update:perPage': [number]
  edit: [RawEquipment]
  'edit-state': [RawEquipment]
  delete: [RawEquipment]
}>()

const usageTypeMap: Record<string, EquipmentUsageType> = {
  EMPRUNTABLE: 'borrowable',
  NON_EMPRUNTABLE: 'fixed',
}

const stateMap: Record<string, EquipmentState> = {
  DISPONIBLE: 'available',
  OCCUPE: 'occupied',
  FONCTIONNEL: 'functional',
  EN_PANNE: 'broken',
  EN_MAINTENANCE: 'maintenance',
  HORS_SERVICE: 'out_of_service',
}

function mapEquipment(r: RawEquipment): Equipment & { raw: RawEquipment } {
  return {
    id: String(r.id),
    name: r.nom,
    code: r.code,
    category: r.category?.nom ?? '—',
    usageType: usageTypeMap[r.usage_type] ?? 'borrowable',
    location: r.usage_type === 'EMPRUNTABLE' ? (r.storage_room?.nom ?? '—') : (r.localisation ?? '—'),
    state: stateMap[r.etat] ?? 'available',
    assignedTo: r.assigned_user ? `${r.assigned_user.prenom} ${r.assigned_user.nom}` : null,
    raw: r,
  }
}

const rows = computed(() => props.equipments.map(mapEquipment))

const pageSize = computed({
  get: () => String(props.perPage),
  set: (value: string) => emit('update:perPage', Number(value)),
})

const rangeStart = computed(() => props.total === 0 ? 0 : (props.page - 1) * props.perPage + 1)
const rangeEnd = computed(() => Math.min(props.page * props.perPage, props.total))
</script>

<template>
  <div class="bg-surface border border-border rounded-xl overflow-hidden">
    <SkeletonTable v-if="isLoading" :rows="perPage > 8 ? 8 : perPage" :columns="8" />
    <p v-else-if="error" class="text-red-400 text-xs p-5">
      {{ error }}
    </p>
    <p v-else-if="rows.length === 0" class="text-muted text-xs p-5">
      Aucun équipement trouvé.
    </p>

    <template v-else>
      <div class="hidden lg:block overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="text-left text-muted text-xs border-b border-border">
              <th class="font-medium py-3 pl-5 pr-4">Nom</th>
              <th class="font-medium py-3 pr-4">Code</th>
              <th class="font-medium py-3 pr-4">Catégorie</th>
              <th class="font-medium py-3 pr-4">Type d'usage</th>
              <th class="font-medium py-3 pr-4">Localisation</th>
              <th class="font-medium py-3 pr-4">État</th>
              <th class="font-medium py-3 pr-4">Assigné à</th>
              <th class="font-medium py-3 pr-5">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="equipment in rows"
              :key="equipment.id"
              class="border-b border-border last:border-0 hover:bg-background/40 transition-colors"
            >
              <td class="py-4 pl-5 pr-4 text-foreground font-medium whitespace-nowrap">{{ equipment.name }}</td>
              <td class="py-4 pr-4 text-muted whitespace-nowrap">{{ equipment.code }}</td>
              <td class="py-4 pr-4 text-foreground whitespace-nowrap">{{ equipment.category }}</td>
              <td class="py-4 pr-4">
                <UsageTypeBadge :type="equipment.usageType" />
              </td>
              <td class="py-4 pr-4 text-foreground whitespace-nowrap">{{ equipment.location }}</td>
              <td class="py-4 pr-4">
                <EquipmentStateBadge :state="equipment.state" />
              </td>
              <td class="py-4 pr-4 text-foreground whitespace-nowrap">
                {{ equipment.assignedTo ?? '—' }}
              </td>
              <td class="py-4 pr-5">
                <div class="flex items-center gap-2">
                  <button
                    v-if="canFullEdit"
                    class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-foreground hover:bg-border/50 transition-colors"
                    title="Modifier"
                    @click="emit('edit', equipment.raw)"
                  >
                    <Pencil class="w-3.5 h-3.5" />
                  </button>
                  <button
                    v-else-if="canEditState"
                    class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-primary-light hover:border-primary/40 transition-colors"
                    title="Modifier l'état"
                    @click="emit('edit-state', equipment.raw)"
                  >
                    <Wrench class="w-3.5 h-3.5" />
                  </button>
                  <button
                    v-if="canDelete"
                    class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-red-400 hover:border-red-400/40 transition-colors"
                    title="Supprimer"
                    @click="emit('delete', equipment.raw)"
                  >
                    <Trash2 class="w-3.5 h-3.5" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Cards mobile / tablette -->
      <div class="lg:hidden divide-y divide-border">
        <div v-for="equipment in rows" :key="equipment.id" class="p-4">
          <div class="flex items-center justify-between mb-2">
            <div>
              <p class="text-foreground font-medium">{{ equipment.name }}</p>
              <p class="text-muted text-xs">Code: {{ equipment.code }}</p>
            </div>
            <UsageTypeBadge :type="equipment.usageType" />
          </div>
          <p class="text-muted text-xs mb-1">{{ equipment.category }} · {{ equipment.location }}</p>
          <div class="flex items-center justify-between mt-2">
            <EquipmentStateBadge :state="equipment.state" />
            <span v-if="equipment.assignedTo" class="text-muted text-xs">{{ equipment.assignedTo }}</span>
          </div>
          <div class="flex items-center gap-2 mt-3">
            <button
              v-if="canFullEdit"
              class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-foreground hover:bg-border/50 transition-colors"
              title="Modifier"
              @click="emit('edit', equipment.raw)"
            >
              <Pencil class="w-3.5 h-3.5" />
            </button>
            <button
              v-else-if="canEditState"
              class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-primary-light hover:border-primary/40 transition-colors"
              title="Modifier l'état"
              @click="emit('edit-state', equipment.raw)"
            >
              <Wrench class="w-3.5 h-3.5" />
            </button>
            <button
              v-if="canDelete"
              class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-red-400 hover:border-red-400/40 transition-colors"
              title="Supprimer"
              @click="emit('delete', equipment.raw)"
            >
              <Trash2 class="w-3.5 h-3.5" />
            </button>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-t border-border">
        <p class="text-muted text-xs">
          Affichage de {{ rangeStart }} à {{ rangeEnd }} sur {{ total }} équipements
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
