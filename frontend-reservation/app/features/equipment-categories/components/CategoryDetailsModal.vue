<script setup lang="ts">
import { ChevronLeft, ChevronRight, X } from 'lucide-vue-next'
import { ref, watch } from 'vue'
import type { RawEquipment } from '~/features/equipments/type'
import { useEquipmentService } from '~/features/equipments/services/equipment.service'
import type { RawEquipmentCategory } from '../type'

const props = defineProps<{
  isOpen: boolean
  category: RawEquipmentCategory | null
}>()

const emit = defineEmits<{
  close: []
}>()

const equipmentService = useEquipmentService()

const equipments = ref<RawEquipment[]>([])
const isLoading = ref(false)
const error = ref<string | null>(null)
const page = ref(1)
const perPage = 10
const total = ref(0)
const lastPage = ref(1)

async function load() {
  if (!props.category) return

  isLoading.value = true
  error.value = null
  try {
    const response = await equipmentService.paginate({
      category_id: props.category.id,
      page: page.value,
      per_page: perPage,
      sort: 'nom',
      direction: 'asc',
    })
    equipments.value = response.data
    total.value = response.total
    lastPage.value = response.last_page
  } catch {
    error.value = 'Impossible de charger les équipements de cette catégorie.'
    equipments.value = []
  } finally {
    isLoading.value = false
  }
}

watch(() => props.isOpen, (open) => {
  if (open) {
    page.value = 1
    load()
  }
})
watch(page, load)

const usageTypeLabels: Record<string, string> = {
  EMPRUNTABLE: 'Empruntable',
  NON_EMPRUNTABLE: 'Non empruntable',
}

const stateLabels: Record<string, string> = {
  DISPONIBLE: 'Disponible',
  FONCTIONNEL: 'Fonctionnel',
  EN_PANNE: 'En panne',
  EN_MAINTENANCE: 'En maintenance',
  HORS_SERVICE: 'Hors service',
}
</script>

<template>
  <Teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60"
      @click.self="emit('close')"
    >
      <div class="bg-surface border border-border rounded-2xl w-full max-w-2xl max-h-[85vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-border">
          <div>
            <h2 class="text-foreground text-lg font-semibold">{{ category?.nom }}</h2>
            <p class="text-muted text-xs mt-0.5">{{ total }} équipement(s) dans cette catégorie</p>
          </div>
          <button class="text-muted hover:text-foreground transition-colors" @click="emit('close')">
            <X class="w-5 h-5" />
          </button>
        </div>

        <div class="p-6">
          <div v-if="isLoading" class="space-y-2">
            <div v-for="i in 4" :key="i" class="flex items-center justify-between gap-3 border border-border rounded-lg px-4 py-3">
              <div class="space-y-2">
                <Skeleton class="h-4 w-32" />
                <Skeleton class="h-3 w-40" />
              </div>
              <Skeleton class="h-3 w-16" />
            </div>
          </div>
          <p v-else-if="error" class="text-red-400 text-sm">
            {{ error }}
          </p>
          <p v-else-if="equipments.length === 0" class="text-muted text-sm">
            Aucun équipement dans cette catégorie.
          </p>

          <template v-else>
            <div class="space-y-2">
              <div
                v-for="equipment in equipments"
                :key="equipment.id"
                class="flex items-center justify-between gap-3 border border-border rounded-lg px-4 py-3"
              >
                <div>
                  <p class="text-foreground font-medium text-sm">{{ equipment.nom }}</p>
                  <p class="text-muted text-xs">{{ equipment.code }} · {{ usageTypeLabels[equipment.usage_type] ?? equipment.usage_type }}</p>
                </div>
                <span class="text-muted text-xs whitespace-nowrap">{{ stateLabels[equipment.etat] ?? equipment.etat }}</span>
              </div>
            </div>

            <div v-if="lastPage > 1" class="flex items-center justify-center gap-2 mt-4">
              <button
                class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-foreground hover:bg-border/50 transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
                :disabled="page === 1"
                @click="page -= 1"
              >
                <ChevronLeft class="w-4 h-4" />
              </button>
              <span class="text-muted text-xs">Page {{ page }} / {{ lastPage }}</span>
              <button
                class="w-8 h-8 flex items-center justify-center rounded-lg border border-border text-muted hover:text-foreground hover:bg-border/50 transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
                :disabled="page === lastPage"
                @click="page += 1"
              >
                <ChevronRight class="w-4 h-4" />
              </button>
            </div>
          </template>
        </div>
      </div>
    </div>
  </Teleport>
</template>
