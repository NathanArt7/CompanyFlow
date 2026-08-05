<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  userName: string
  subtitle?: string
}>()

const formattedDate = computed(() => {
  const date = new Date()
  const formatted = new Intl.DateTimeFormat('fr-FR', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  }).format(date)

  // Met en majuscule la première lettre du mois si besoin, et retire le point après l'abréviation
  return formatted.replace('.', '')
})
</script>

<template>
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <h1 class="text-foreground text-2xl md:text-3xl font-bold flex items-center gap-2">
        Bonjour {{ userName }}
        <span>👋</span>
      </h1>
      <p class="text-muted text-sm mt-1">
        {{ subtitle ?? "Voici un aperçu de l'activité de la plateforme aujourd'hui." }}
      </p>
    </div>

    <div class="flex items-center gap-2 bg-surface border border-border rounded-lg px-4 py-2 text-sm text-foreground shrink-0 self-start sm:self-auto">
      {{ formattedDate }}
    </div>
  </div>
</template>