<script setup lang="ts">
import { Users, ShieldCheck, UserCog, Wrench, User } from 'lucide-vue-next'
import { ref, onMounted } from 'vue'
import type { UserStat } from '../types'
import { useUserService } from '../services/user.service'

const userService = useUserService()

const isLoading = ref(true)
const error = ref<string | null>(null)
const stats = ref<UserStat[]>([])

function pct(part: number, total: number): number {
  return total > 0 ? Math.round((part / total) * 100) : 0
}

async function load() {
  isLoading.value = true
  error.value = null
  try {
    const data = await userService.getStats()

    stats.value = [
      {
        icon: Users,
        iconBg: 'bg-primary/20 text-primary-light',
        value: data.total,
        label: 'Total utilisateurs',
        subtext: 'Tous rôles confondus',
        barColor: '',
        percentage: 0,
      },
      {
        icon: ShieldCheck,
        iconBg: 'bg-blue-500/20 text-blue-400',
        value: data.admins,
        label: 'Admins',
        subtext: `${pct(data.admins, data.total)}% du total`,
        barColor: 'bg-blue-500',
        percentage: pct(data.admins, data.total),
      },
      {
        icon: UserCog,
        iconBg: 'bg-cyan-500/20 text-cyan-400',
        value: data.super_employes,
        label: 'Super employés',
        subtext: `${pct(data.super_employes, data.total)}% du total`,
        barColor: 'bg-cyan-500',
        percentage: pct(data.super_employes, data.total),
      },
      {
        icon: Wrench,
        iconBg: 'bg-orange-500/20 text-orange-500',
        value: data.techniciens,
        label: 'Techniciens',
        subtext: `${pct(data.techniciens, data.total)}% du total`,
        barColor: 'bg-orange-500',
        percentage: pct(data.techniciens, data.total),
      },
      {
        icon: User,
        iconBg: 'bg-green-500/20 text-green-500',
        value: data.employes,
        label: 'Employés',
        subtext: `${pct(data.employes, data.total)}% du total`,
        barColor: 'bg-green-500',
        percentage: pct(data.employes, data.total),
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
  <div v-if="isLoading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
    <SkeletonStatCard v-for="i in 5" :key="i" with-bar />
  </div>
  <p v-else-if="error" class="text-red-400 text-xs">
    {{ error }}
  </p>
  <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
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
