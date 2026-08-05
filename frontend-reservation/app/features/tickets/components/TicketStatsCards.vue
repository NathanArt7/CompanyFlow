<script setup lang="ts">
import { Ticket, Loader2, CheckCircle2, Clock } from 'lucide-vue-next'
import { computed } from 'vue'
import type { TicketStats } from '../type'

const props = defineProps<{
  stats: TicketStats | null
  isLoading: boolean
}>()

const cards = computed(() => [
  { icon: Ticket, iconBg: 'bg-primary/20 text-primary-light', value: props.stats?.total_ce_mois ?? 0, label: 'Tickets total', subtext: 'Ce mois' },
  { icon: Loader2, iconBg: 'bg-blue-500/20 text-blue-400', value: props.stats?.en_cours ?? 0, label: 'Tickets en cours' },
  { icon: CheckCircle2, iconBg: 'bg-green-500/20 text-green-500', value: props.stats?.resolus ?? 0, label: 'Tickets résolus' },
  { icon: Clock, iconBg: 'bg-orange-500/20 text-orange-500', value: props.stats?.restants ?? 0, label: 'Tickets restants' },
])
</script>

<template>
  <div v-if="isLoading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <SkeletonStatCard v-for="i in 4" :key="i" />
  </div>
  <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div v-for="card in cards" :key="card.label" class="bg-surface border border-border rounded-xl p-5">
      <div class="w-11 h-11 rounded-xl flex items-center justify-center mb-3" :class="card.iconBg">
        <component :is="card.icon" class="w-5 h-5" />
      </div>
      <p class="text-foreground text-2xl font-bold">{{ card.value }}</p>
      <p class="text-foreground text-sm">{{ card.label }}</p>
      <p v-if="card.subtext" class="text-muted text-xs mt-1">{{ card.subtext }}</p>
    </div>
  </div>
</template>
