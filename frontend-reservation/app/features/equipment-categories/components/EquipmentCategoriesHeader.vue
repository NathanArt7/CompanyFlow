<script setup lang="ts">
import { ArrowLeft, Plus } from 'lucide-vue-next'

const emit = defineEmits<{
  'create-click': []
}>()
</script>

<template>
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div class="flex items-start gap-3">
      <NuxtLink
        to="/equipments"
        class="w-9 h-9 flex items-center justify-center rounded-lg border border-border text-muted hover:text-foreground hover:bg-border/50 transition-colors shrink-0 mt-0.5"
        title="Retour aux équipements"
      >
        <ArrowLeft class="w-4 h-4" />
      </NuxtLink>
      <div>
        <h1 class="text-foreground text-2xl md:text-3xl font-bold">Catégories d'équipements</h1>
        <p class="text-muted text-sm mt-1">Organisez votre parc matériel en créant des catégories personnalisées.</p>
      </div>
    </div>

    <button
      class="flex items-center gap-2 bg-primary hover:bg-primary-hover text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors shrink-0"
      @click="emit('create-click')"
    >
      <Plus class="w-4 h-4" />
      Nouvelle catégorie
    </button>
  </div>
</template>
