<script setup lang="ts">
import { Monitor, PackageCheck, Package, Users, XCircle, Wrench } from 'lucide-vue-next'
import { ref, computed, onMounted } from 'vue'
import type { EquipmentStat } from '../types'
import { useEquipmentService } from '../services/equipment.service'

const equipmentService = useEquipmentService()
const authStore = useAuthStore()

// Le Technicien n'a pas besoin de la vue "occupation des empruntables" (gestion des
// réservations de matériel), hors de son périmètre.
const isTechnicien = computed(() => authStore.user?.role === 'Technicien')

const isLoading = ref(true)
const error = ref<string | null>(null)
const stats = ref<EquipmentStat[]>([])

function pct(part: number, total: number): number {
  return total > 0 ? Math.round((part / total) * 100) : 0
}

async function load() {
  isLoading.value = true
  error.value = null
  try {
    const data = await equipmentService.getStats()

    stats.value = [
      {
        icon: Monitor,
        iconBg: 'bg-primary/20 text-primary-light',
        value: data.total,
        label: 'Total équipements',
        subtext: 'Toutes catégories confondues',
        barColor: '',
        percentage: 0,
      },
      {
        icon: PackageCheck,
        iconBg: 'bg-green-500/20 text-green-500',
        value: data.borrowable,
        label: 'Équipements empruntables',
        subtext: `${pct(data.borrowable, data.total)}% du total`,
        barColor: 'bg-green-500',
        percentage: pct(data.borrowable, data.total),
      },
      {
        icon: Package,
        iconBg: 'bg-purple-500/20 text-purple-400',
        value: data.non_borrowable,
        label: 'Équipements non-empruntables',
        subtext: `${pct(data.non_borrowable, data.total)}% du total`,
        barColor: 'bg-purple-500',
        percentage: pct(data.non_borrowable, data.total),
      },
      ...(isTechnicien.value ? [] : [{
        icon: Users,
        iconBg: 'bg-orange-500/20 text-orange-500',
        value: data.borrowable_occupied,
        label: 'Empruntables occupés',
        subtext: `${pct(data.borrowable_occupied, data.borrowable)}% des empruntables`,
        barColor: 'bg-orange-500',
        percentage: pct(data.borrowable_occupied, data.borrowable),
      }]),
      {
        icon: XCircle,
        iconBg: 'bg-red-500/20 text-red-400',
        value: data.broken,
        label: 'En panne',
        subtext: `${pct(data.broken, data.total)}% du total`,
        barColor: 'bg-red-500',
        percentage: pct(data.broken, data.total),
      },
      {
        icon: Wrench,
        iconBg: 'bg-blue-500/20 text-blue-400',
        value: data.in_maintenance,
        label: 'En maintenance',
        subtext: `${pct(data.in_maintenance, data.total)}% du total`,
        barColor: 'bg-blue-500',
        percentage: pct(data.in_maintenance, data.total),
      },
    ]
  } catch {
    error.value = 'Impossible de charger les statistiques.'
  } finally {
    isLoading.value = false
  }
}

onMounted(load)

defineExpose({
  refresh: load,
})
</script>

<template>
  <div v-if="isLoading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" :class="isTechnicien ? 'xl:grid-cols-5' : 'xl:grid-cols-6'">
    <SkeletonStatCard v-for="i in (isTechnicien ? 5 : 6)" :key="i" with-bar />
  </div>
  <p v-else-if="error" class="text-red-400 text-xs">
    {{ error }}
  </p>
  <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" :class="isTechnicien ? 'xl:grid-cols-5' : 'xl:grid-cols-6'">
    <div v-for="stat in stats" :key="stat.label" class="bg-surface border border-border rounded-xl p-5">
      <div class="w-11 h-11 rounded-xl flex items-center justify-center mb-3" :class="stat.iconBg">
        <component :is="stat.icon" class="w-5 h-5" />
      </div>
      <p class="text-foreground text-2xl font-bold">{{ stat.value }}</p>
      <p class="text-foreground text-sm">{{ stat.label }}</p>
      <p class="text-muted text-xs mt-1">{{ stat.subtext }}</p>

      <div v-if="stat.barColor" class="w-full h-1 bg-background rounded-full overflow-hidden mt-3">
        <div class="h-full rounded-full" :class="stat.barColor" :style="{ width: `${stat.percentage}%` }" />
      </div>
    </div>
  </div>
</template>
