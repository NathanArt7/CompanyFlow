<script setup lang="ts">
import { Settings } from 'lucide-vue-next'
</script>

<template>
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <h1 class="text-foreground text-2xl md:text-3xl font-bold">Disponibilités</h1>
      <p class="text-muted text-sm mt-1">
        Consultez la disponibilité des salles pour réserver au bon moment.
      </p>
    </div>

    <NuxtLink
      to="/settings"
      class="flex items-center gap-2 bg-primary hover:bg-primary-hover text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors shrink-0"
    >
      <Settings class="w-4 h-4" />
      Voir les paramètres
    </NuxtLink>
  </div>
</template>