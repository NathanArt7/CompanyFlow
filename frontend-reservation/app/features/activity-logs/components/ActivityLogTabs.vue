<script setup lang="ts">
import { Calendar, DoorOpen, Monitor, Users, Ticket } from 'lucide-vue-next'
import type { ActivityModule } from '../type'

const props = defineProps<{
  activeModule: ActivityModule
}>()

const emit = defineEmits<{
  'update:activeModule': [ActivityModule]
}>()

const tabs: { key: ActivityModule, label: string, icon: typeof Calendar }[] = [
  { key: 'RESERVATION', label: 'Réservations', icon: Calendar },
  { key: 'SALLE', label: 'Salles', icon: DoorOpen },
  { key: 'EQUIPEMENT', label: 'Équipements', icon: Monitor },
  { key: 'UTILISATEUR', label: 'Utilisateurs', icon: Users },
  { key: 'TICKET', label: 'Tickets', icon: Ticket },
]
</script>

<template>
  <div class="inline-flex flex-wrap items-center gap-1 bg-surface border border-border rounded-xl p-1.5">
    <button
      v-for="tab in tabs"
      :key="tab.key"
      class="flex items-center gap-2 px-3.5 py-2 rounded-lg text-sm font-medium whitespace-nowrap transition-colors"
      :class="props.activeModule === tab.key
        ? 'bg-primary/15 text-primary-light'
        : 'text-muted hover:text-foreground hover:bg-border/50'"
      @click="emit('update:activeModule', tab.key)"
    >
      <component :is="tab.icon" class="w-4 h-4" />
      {{ tab.label }}
    </button>
  </div>
</template>
