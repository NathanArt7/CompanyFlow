<script setup lang="ts">
import { AlertTriangle, Wrench, UserCog, Building2, ChevronRight } from 'lucide-vue-next'
import { onMounted, ref } from 'vue'
import type { TodoItem } from '../type'
import { useDashboardService } from '../services/dashboard.service'

const dashboardService = useDashboardService()
const isLoading = ref(true)
const error = ref<string | null>(null)

// Pas de système de tickets côté backend : cette ligne reste une donnée statique.
const ticketsTodo: TodoItem = {
  icon: AlertTriangle,
  iconColor: 'text-orange-500',
  label: '2 nouveaux tickets créés',
  actionLabel: 'Voir',
  to: '/tickets',
}

const todos = ref<TodoItem[]>([ticketsTodo])

onMounted(async () => {
  try {
    const alerts = await dashboardService.getAlerts()
    const dynamic: TodoItem[] = []

    if (alerts.equipments_in_maintenance > 0) {
      dynamic.push({
        icon: Wrench,
        iconColor: 'text-blue-400',
        label: `${alerts.equipments_in_maintenance} équipement${alerts.equipments_in_maintenance > 1 ? 's' : ''} en maintenance`,
        actionLabel: 'Voir',
        to: '/equipments',
      })
    }

    if (alerts.unactivated_users > 0) {
      dynamic.push({
        icon: UserCog,
        iconColor: 'text-primary-light',
        label: `${alerts.unactivated_users} utilisateur${alerts.unactivated_users > 1 ? 's' : ''} non activé${alerts.unactivated_users > 1 ? 's' : ''}`,
        actionLabel: 'Voir',
        to: '/users',
      })
    }

    if (alerts.rooms_in_maintenance > 0) {
      dynamic.push({
        icon: Building2,
        iconColor: 'text-red-400',
        label: `${alerts.rooms_in_maintenance} salle${alerts.rooms_in_maintenance > 1 ? 's' : ''} en maintenance`,
        actionLabel: 'Voir',
        to: '/rooms',
      })
    }

    todos.value = [ticketsTodo, ...dynamic]
  } catch {
    error.value = 'Impossible de charger les alertes.'
  } finally {
    isLoading.value = false
  }
})
</script>

<template>
  <div class="bg-surface border border-border rounded-xl p-4 h-full flex flex-col">
    <h2 class="text-foreground font-semibold text-sm mb-2">À traiter</h2>

    <div v-if="isLoading" class="flex-1 flex flex-col justify-around gap-2">
      <div v-for="i in 3" :key="i" class="flex items-center justify-between gap-3">
        <Skeleton class="h-3 w-32" />
        <Skeleton class="h-3 w-10" />
      </div>
    </div>
    <p v-else-if="error" class="text-red-400 text-xs">
      {{ error }}
    </p>
    <div v-else class="flex-1 flex flex-col justify-around">
      <NuxtLink
        v-for="(todo, index) in todos"
        :key="index"
        :to="todo.to"
        class="flex items-center justify-between gap-3 group"
      >
        <div class="flex items-center gap-2.5 min-w-0">
          <component :is="todo.icon" class="w-3.5 h-3.5 shrink-0" :class="todo.iconColor" />
          <span class="text-foreground text-xs truncate">{{ todo.label }}</span>
        </div>
        <span class="flex items-center gap-1 text-muted text-xs shrink-0 group-hover:text-foreground transition-colors">
          {{ todo.actionLabel }}
          <ChevronRight class="w-3 h-3" />
        </span>
      </NuxtLink>
    </div>
  </div>
</template>