<script setup lang="ts">
import { Cloud, Check } from 'lucide-vue-next'
</script>

<template>
  <div class="bg-surface border border-border rounded-xl p-5">
    <div class="flex items-start justify-between mb-4">
      <div class="flex items-start gap-3">
        <div class="w-9 h-9 rounded-lg bg-primary/15 flex items-center justify-center shrink-0">
          <Cloud class="w-4.5 h-4.5 text-primary-light" />
        </div>
        <div>
          <h2 class="text-foreground font-semibold">Sauvegarde</h2>
          <p class="text-muted text-xs mt-0.5">Gérez vos sauvegardes de données.</p>
        </div>
      </div>
      <NuxtLink
        to="/settings/backup"
        class="bg-background border border-border rounded-lg px-3 py-1.5 text-sm text-foreground hover:border-primary/40 transition-colors shrink-0"
      >
        Gérer
      </NuxtLink>
    </div>

    <div class="space-y-3">
      <div class="flex items-center justify-between text-sm">
        <span class="text-muted">Dernière sauvegarde</span>
        <span class="text-foreground">24 mai 2026, 02:15</span>
      </div>
      <div class="flex items-center justify-between text-sm">
        <span class="text-muted">Prochaine sauvegarde</span>
        <span class="text-foreground">25 mai 2026, 02:15</span>
      </div>
      <div class="flex items-center justify-between text-sm">
        <span class="text-muted">Fréquence</span>
        <span class="text-foreground">Quotidienne</span>
      </div>
      <div class="flex items-center justify-between text-sm">
        <span class="text-muted">Emplacement</span>
        <span class="text-foreground">Cloud sécurisé</span>
      </div>
    </div>

    <div class="flex justify-end mt-4">
      <span class="inline-flex items-center gap-1.5 text-xs font-medium text-green-500 bg-green-500/10 px-2.5 py-1 rounded-full">
        <Check class="w-3 h-3" />
        À jour
      </span>
    </div>
  </div>
</template>