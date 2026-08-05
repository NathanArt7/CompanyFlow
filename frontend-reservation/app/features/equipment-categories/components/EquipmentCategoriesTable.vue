<script setup lang="ts">
import { ChevronLeft, ChevronRight, Info, Pencil, Trash2 } from 'lucide-vue-next'
import { computed } from 'vue'
import type { RawEquipmentCategory } from '../type'

const props = defineProps<{
  categories: RawEquipmentCategory[]
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
  edit: [RawEquipmentCategory]
  delete: [RawEquipmentCategory]
  details: [RawEquipmentCategory]
}>()

function formatDate(value: string) {
  return new Date(value).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' })
}

const pageSize = computed({
  get: () => String(props.perPage),
  set: (value: string) => emit('update:perPage', Number(value)),
})

const rangeStart = computed(() => props.total === 0 ? 0 : (props.page - 1) * props.perPage + 1)
const rangeEnd = computed(() => Math.min(props.page * props.perPage, props.total))
</script>

<template>
  <div class="bg-surface border border-border rounded-xl overflow-hidden">
    <SkeletonTable v-if="isLoading" :rows="perPage > 8 ? 8 : perPage" :columns="5" />
    <p v-else-if="error" class="text-red-400 text-xs p-5">
      {{ error }}
    </p>
    <p v-else-if="categories.length === 0" class="text-muted text-xs p-5">
      Aucune catégorie trouvée.
    </p>

    <template v-else>
      <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="text-left text-muted text-xs border-b border-border">
              <th class="font-medium py-3 pl-5 pr-4">Nom</th>
              <th class="font-medium py-3 pr-4">Description</th>
              <th class="font-medium py-3 pr-4">Nb d'équipements</th>
              <th class="font-medium py-3 pr-4">Créée le</th>
              <th class="font-medium py-3 pr-5">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="category in categories"
              :key="category.id"
              class="border-b border-border last:border-0 hover:bg-background/40 transition-colors"
            >
              <td class="py-4 pl-5 pr-4">
                <span class="text-foreground font-medium">{{ category.nom }}</span>
              </td>
              <td class="py-4 pr-4 text-muted max-w-xs whitespace-normal break-words">{{ category.description || '—' }}</td>
              <td class="py-4 pr-4 text-foreground">{{ category.equipments_count ?? 0 }}</td>
              <td class="py-4 pr-4 text-muted whitespace-nowrap">{{ formatDate(category.created_at) }}</td>
              <td class="py-4 pr-5 relative">
                <ActionsMenu v-slot="{ close }" bordered>
                  <button
                    class="w-full flex items-center gap-2 px-3 py-2.5 text-sm text-foreground hover:bg-border/50 transition-colors"
                    @click="emit('details', category); close()"
                  >
                    <Info class="w-3.5 h-3.5" />
                    Détails de la catégorie
                  </button>
                  <button
                    class="w-full flex items-center gap-2 px-3 py-2.5 text-sm text-foreground hover:bg-border/50 transition-colors"
                    @click="emit('edit', category); close()"
                  >
                    <Pencil class="w-3.5 h-3.5" />
                    Modifier
                  </button>
                  <button
                    class="w-full flex items-center gap-2 px-3 py-2.5 text-sm text-red-400 hover:bg-border/50 transition-colors"
                    @click="emit('delete', category); close()"
                  >
                    <Trash2 class="w-3.5 h-3.5" />
                    Supprimer
                  </button>
                </ActionsMenu>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Cards mobile -->
      <div class="md:hidden divide-y divide-border">
        <div v-for="category in categories" :key="category.id" class="p-4">
          <div class="flex items-center justify-between mb-2">
            <span class="text-foreground font-medium">{{ category.nom }}</span>
            <ActionsMenu v-slot="{ close }" bordered>
              <button
                class="w-full flex items-center gap-2 px-3 py-2.5 text-sm text-foreground hover:bg-border/50 transition-colors"
                @click="emit('details', category); close()"
              >
                <Info class="w-3.5 h-3.5" />
                Détails de la catégorie
              </button>
              <button
                class="w-full flex items-center gap-2 px-3 py-2.5 text-sm text-foreground hover:bg-border/50 transition-colors"
                @click="emit('edit', category); close()"
              >
                <Pencil class="w-3.5 h-3.5" />
                Modifier
              </button>
              <button
                class="w-full flex items-center gap-2 px-3 py-2.5 text-sm text-red-400 hover:bg-border/50 transition-colors"
                @click="emit('delete', category); close()"
              >
                <Trash2 class="w-3.5 h-3.5" />
                Supprimer
              </button>
            </ActionsMenu>
          </div>
          <p v-if="category.description" class="text-muted text-xs mb-2 whitespace-normal break-words">{{ category.description }}</p>
          <div class="flex items-center justify-between text-xs text-muted">
            <span>{{ category.equipments_count ?? 0 }} équipement(s)</span>
            <span>{{ formatDate(category.created_at) }}</span>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-t border-border">
        <p class="text-muted text-xs">
          Affichage de {{ rangeStart }} à {{ rangeEnd }} sur {{ total }} catégories
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
