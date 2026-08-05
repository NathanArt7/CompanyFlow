<script setup lang="ts">
</script>

<template>
  <section class="relative overflow-hidden w-full px-6 py-20 md:py-24 flex flex-col items-center text-center">
    <!-- Glow violet en fond -->
    <div
      class="pointer-events-none absolute top-0 left-1/2 -translate-x-1/2 w-[320px] h-[240px] md:w-[600px] md:h-[400px] bg-primary/30 rounded-full blur-[80px] md:blur-[120px]"
    />

    <!-- Contenu existant, inchangé, juste passé au-dessus du glow -->
    <div class="relative z-10 flex flex-col items-center">
      <!-- <div class="flex items-center gap-2 px-4 py-1.5 rounded-full border border-border text-xs text-muted mb-8">
        <span class="w-1.5 h-1.5 rounded-full bg-green-500" />
          Lancement public — Juillet 2026
      </div> -->

      <h1 class="text-3xl sm:text-4xl md:text-6xl font-bold text-foreground max-w-3xl leading-tight">
        Organisez votre entreprise
        <span class="text-primary-light block">efficacement.</span>
      </h1>

      <p class="text-muted text-base md:text-lg max-w-xl mt-5 md:mt-6 px-2">
        CompanyPilot centralise les réservations de salles, la gestion du matériel
        et la collaboration interne sur une seule plateforme.
      </p>

      <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4 mt-8 w-full sm:w-auto px-4 sm:px-0">
        <NuxtLink
          to="/signup"
          class="bg-primary hover:bg-primary-hover text-white font-medium px-6 py-3 rounded-lg transition-colors text-center"
        >
          Commencer gratuitement
        </NuxtLink>
        <NuxtLink
          to="/login"
          class="bg-surface hover:bg-border text-foreground font-medium px-6 py-3 rounded-lg border border-border transition-colors text-center"
        >
          Se connecter
        </NuxtLink>
      </div>

      <p class="text-muted text-xs mt-4">Aucune carte bancaire requise.</p>
    </div>
  </section>
</template>