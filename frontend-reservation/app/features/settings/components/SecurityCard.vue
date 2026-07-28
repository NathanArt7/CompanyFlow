<script setup lang="ts">
import { Shield } from 'lucide-vue-next'
</script>

<template>
  <div class="bg-surface border border-border rounded-xl p-5">
    <div class="flex items-start justify-between mb-4">
      <div class="flex items-start gap-3">
        <div class="w-9 h-9 rounded-lg bg-primary/15 flex items-center justify-center shrink-0">
          <Shield class="w-4.5 h-4.5 text-primary-light" />
        </div>
        <div>
          <h2 class="text-foreground font-semibold">Sécurité</h2>
          <p class="text-muted text-xs mt-0.5">Paramètres de sécurité et de session.</p>
        </div>
      </div>
      <NuxtLink
        to="/settings/security"
        class="bg-background border border-border rounded-lg px-3 py-1.5 text-sm text-foreground hover:border-primary/40 transition-colors shrink-0"
      >
        Gérer
      </NuxtLink>
    </div>

    <div class="flex items-center justify-between">
      <div>
        <p class="text-foreground text-sm font-medium">Authentification à deux facteurs</p>
        <p class="text-muted text-xs mt-0.5">Renforcez la sécurité de votre compte</p>
      </div>
      <span class="text-xs font-medium text-green-500 bg-green-500/10 px-2.5 py-1 rounded-full shrink-0">
        Activée
      </span>
    </div>
  </div>
</template>