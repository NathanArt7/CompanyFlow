<script setup lang="ts">
import { Plus, Upload } from 'lucide-vue-next'
</script>

<template>
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <h1 class="text-foreground text-2xl md:text-3xl font-bold">Utilisateurs</h1>
      <p class="text-muted text-sm mt-1">Gérez tous les utilisateurs de votre entreprise.</p>
    </div>

    <div class="flex items-center gap-3 shrink-0">
      <button class="flex items-center gap-2 bg-primary hover:bg-primary-hover text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors">
        <Plus class="w-4 h-4" />
        Ajouter un utilisateur
      </button>

      <button class="flex items-center gap-2 bg-surface hover:bg-border border border-border text-foreground text-sm font-medium px-4 py-2.5 rounded-lg transition-colors">
        <Upload class="w-4 h-4" />
        Exporter
      </button>
    </div>
  </div>
</template>