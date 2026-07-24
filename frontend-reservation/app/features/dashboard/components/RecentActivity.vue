<script setup lang="ts">
import { Calendar, UserPlus, Monitor, Settings } from 'lucide-vue-next'
import type { ActivityItem } from '../types'

const activities: ActivityItem[] = [
  {
    icon: Calendar,
    iconBg: 'bg-primary/15 text-primary-light',
    title: 'Nouvelle réservation',
    description: 'Salle de réunion B par Sarah Johnson',
    time: 'il y a 5 min',
  },
  {
    icon: UserPlus,
    iconBg: 'bg-blue-500/15 text-blue-400',
    title: 'Nouvel utilisateur',
    description: "Thomas Bernard a rejoint l'entreprise",
    time: 'il y a 25 min',
  },
  {
    icon: Monitor,
    iconBg: 'bg-purple-500/15 text-purple-400',
    title: 'Équipement emprunté',
    description: 'Vidéoprojecteur par Marc Lorem',
    time: 'il y a 1 h',
  },
  {
    icon: Settings,
    iconBg: 'bg-orange-500/15 text-orange-400',
    title: 'Paramètres mis à jour',
    description: 'Horaires de service modifiés',
    time: 'il y a 2 h',
  },
]
</script>

<template>
  <div class="bg-surface border border-border rounded-xl p-4">
    <h2 class="text-foreground font-semibold mb-5">Activité récente</h2>

    <div class="space-y-5">
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