<script setup lang="ts">
import { AlertTriangle, Wrench, UserCog, Building2, ChevronRight } from 'lucide-vue-next'
import type { TodoItem } from '../types'

const todos: TodoItem[] = [
  {
    icon: AlertTriangle,
    iconColor: 'text-orange-500',
    label: '2 nouveaux tickets créés',
    actionLabel: 'Voir',
    to: '/tickets',
  },
  {
    icon: Wrench,
    iconColor: 'text-blue-400',
    label: '1 équipement en maintenance',
    actionLabel: 'Voir',
    to: '/equipments',
  },
  {
    icon: UserCog,
    iconColor: 'text-primary-light',
    label: '1 utilisateur non activé',
    actionLabel: 'Voir',
    to: '/users',
  },
  {
    icon: Building2,
    iconColor: 'text-red-400',
    label: '2 salles mises en maintenance',
    actionLabel: 'Voir',
    to: '/rooms',
  },
]
</script>

<template>
  <div class="bg-surface border border-border rounded-xl p-4 h-full flex flex-col">
    <h2 class="text-foreground font-semibold text-sm mb-2">À traiter</h2>

    <div class="flex-1 flex flex-col justify-around">
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