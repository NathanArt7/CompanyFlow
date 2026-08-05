<script setup lang="ts">
import { ShieldOff } from 'lucide-vue-next'
</script>

<template>
  <div class="relative w-full min-h-screen flex items-center justify-center overflow-hidden px-4 py-12 md:py-16">
    <!-- Glow décoratif -->
    <div
      class="pointer-events-none absolute top-0 left-1/2 -translate-x-1/2 w-[320px] h-[240px] md:w-[600px] md:h-[400px] bg-red-500/20 rounded-full blur-[80px] md:blur-[120px]"
    />

    <div class="relative z-10 w-full max-w-md">
      <div class="bg-surface border border-border rounded-2xl px-6 py-8 md:px-10 md:py-10 text-center">
        <div class="w-14 h-14 rounded-xl bg-red-500/15 flex items-center justify-center mx-auto mb-6">
          <ShieldOff class="w-6 h-6 text-red-400" />
        </div>

        <h1 class="text-foreground text-2xl md:text-3xl font-bold leading-snug">
          Compte désactivé
        </h1>
        <p class="text-muted text-sm mt-3 leading-relaxed">
          Votre compte a été désactivé par un administrateur. Contactez-le pour plus d'informations
          ou pour demander la réactivation de votre accès.
        </p>

        <NuxtLink
          to="/login"
          class="inline-block w-full bg-primary hover:bg-primary-hover text-white font-medium py-3 rounded-lg transition-colors mt-8"
        >
          Retour à la connexion
        </NuxtLink>
      </div>
    </div>
  </div>
</template>
