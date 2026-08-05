<script setup lang="ts">
import { Info } from 'lucide-vue-next'
import { ref } from 'vue'

const isModalOpen = ref(false)
</script>

<template>
  <div class="bg-surface border border-border rounded-xl p-5 flex items-start justify-between gap-4">
    <div class="flex items-start gap-3">
      <div class="w-9 h-9 rounded-lg bg-primary/15 flex items-center justify-center shrink-0">
        <Info class="w-4.5 h-4.5 text-primary-light" />
      </div>
      <div>
        <h2 class="text-foreground font-semibold">À propos</h2>
        <p class="text-muted text-xs mt-0.5">Informations sur l'application.</p>
      </div>
    </div>
    <button
      class="bg-background border border-border rounded-lg px-3 py-1.5 text-sm text-foreground hover:border-primary/40 transition-colors shrink-0"
      @click="isModalOpen = true"
    >
      Voir
    </button>

    <Teleport to="body">
      <div
        v-if="isModalOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60"
        @click.self="isModalOpen = false"
      >
        <div class="bg-surface border border-border rounded-2xl w-full max-w-sm p-6">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-lg bg-primary/15 flex items-center justify-center shrink-0">
              <Info class="w-5 h-5 text-primary-light" />
            </div>
            <h2 class="text-foreground text-lg font-semibold">CompanyPilot</h2>
          </div>
          <p class="text-muted text-sm leading-relaxed">
            CompanyPilot est une plateforme de gestion des réservations de salles et de matériel
            pour les entreprises : disponibilités en temps réel, gestion des utilisateurs et des
            équipements, et suivi des réservations au sein de votre organisation.
          </p>
          <button
            class="w-full mt-6 bg-background hover:bg-border text-foreground font-medium py-2.5 rounded-lg border border-border transition-colors"
            @click="isModalOpen = false"
          >
            Fermer
          </button>
        </div>
      </div>
    </Teleport>
  </div>
</template>
