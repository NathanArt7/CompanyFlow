<!-- app/features/marketing/components/MarketingFooter.vue -->
<script setup lang="ts">
import { LayoutGrid } from 'lucide-vue-next'

const currentYear = new Date().getFullYear()
</script>

<template>
  <footer class="w-full border-t border-border px-6 md:px-10 py-12 md:py-16">
    <div class="max-w-6xl mx-auto">
      <!-- Bloc principal : logo + description + liens -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-8 md:gap-10">
        <!-- Logo + description -->
        <div class="sm:col-span-2 lg:col-span-2">
          <div class="flex items-center gap-2 mb-4">
            <div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center">
              <LayoutGrid class="w-4 h-4 text-white" />
            </div>
            <span class="text-foreground font-semibold text-lg">CompanyPilot</span>
          </div>
          <p class="text-muted text-sm max-w-xs leading-relaxed">
            Centralisez les réservations de salles, la gestion des équipements
            et la collaboration interne.
          </p>
        </div>

        <!-- Colonne Produit -->
        <div>
          <h3 class="text-foreground font-semibold text-sm mb-4">Produit</h3>
          <ul class="space-y-3">
            <li>
              <a href="#features" class="text-muted text-sm hover:text-foreground transition-colors">
                Fonctionnalités
              </a>
            </li>
            <li>
              <a href="#works" class="text-muted text-sm hover:text-foreground transition-colors">
                Fonctionnement
              </a>
            </li>
            <li>
              <a href="#cta" class="text-muted text-sm hover:text-foreground transition-colors">
                Tarifs
              </a>
            </li>
          </ul>
        </div>

        <!-- Colonne Entreprise -->
        <div>
          <h3 class="text-foreground font-semibold text-sm mb-4">Entreprise</h3>
          <ul class="space-y-3">
            <li>
              <NuxtLink to="/about" class="text-muted text-sm hover:text-foreground transition-colors">
                À propos
              </NuxtLink>
            </li>
            <li>
              <NuxtLink to="/contact" class="text-muted text-sm hover:text-foreground transition-colors">
                Contact
              </NuxtLink>
            </li>
          </ul>
        </div>

        <!-- Colonne Légal -->
        <div>
          <h3 class="text-foreground font-semibold text-sm mb-4">Légal</h3>
          <ul class="space-y-3">
            <li>
              <NuxtLink to="/privacy" class="text-muted text-sm hover:text-foreground transition-colors">
                Politique de confidentialité
              </NuxtLink>
            </li>
            <li>
              <NuxtLink to="/terms" class="text-muted text-sm hover:text-foreground transition-colors">
                Conditions d'utilisation
              </NuxtLink>
            </li>
          </ul>
        </div>
      </div>

      <!-- Bas de page -->
      <div class="border-t border-border mt-10 md:mt-12 pt-6 md:pt-8 flex flex-col md:flex-row items-center justify-between gap-3 md:gap-4 text-center md:text-left">
        <p class="text-muted text-xs">
          © {{ currentYear }} CompanyPilot. Tous droits réservés.
        </p>
        <p class="text-muted text-xs">
          Made by Nathan.Art
        </p>
      </div>
    </div>
  </footer>
</template>