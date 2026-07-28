<script setup lang="ts">
import type { ActivityItem } from '../type'

// Aucun journal d'activité n'existe côté backend pour l'instant (pas de table, pas de package
// d'audit-log) : ce widget reste vide plutôt que d'afficher de fausses données.
const activities: ActivityItem[] = []
</script>

<template>
  <div class="bg-surface border border-border rounded-xl p-4">
    <h2 class="text-foreground font-semibold mb-5">Activité récente</h2>

    <p v-if="activities.length === 0" class="text-muted text-xs">
      Aucune activité récente pour le moment.
    </p>
    <div v-else class="space-y-5">
      <div
        v-for="(activity, index) in activities"
        :key="index"
        class="flex items-start gap-3"
      >
        <div
          class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0"
          :class="activity.iconBg"
        >
          <component :is="activity.icon" class="w-4 h-4" />
        </div>

        <div class="min-w-0 flex-1">
          <div class="flex items-center justify-between gap-2">
            <p class="text-foreground text-sm font-medium">{{ activity.title }}</p>
            <span class="text-muted text-xs whitespace-nowrap shrink-0">{{ activity.time }}</span>
          </div>
          <p class="text-muted text-xs mt-0.5 truncate">{{ activity.description }}</p>
        </div>
      </div>
    </div>
  </div>
</template>