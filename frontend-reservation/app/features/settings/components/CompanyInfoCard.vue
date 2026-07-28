<script setup lang="ts">
import { Pencil, Building2 } from 'lucide-vue-next'
</script>

<template>
  <div class="bg-surface border border-border rounded-xl p-5">
    <div class="flex items-center justify-between mb-1">
      <h2 class="text-foreground font-semibold">Informations de l'entreprise</h2>
      <button class="flex items-center gap-2 bg-background border border-border rounded-lg px-3 py-1.5 text-sm text-foreground hover:border-primary/40 transition-colors">
        <Pencil class="w-3.5 h-3.5" />
        Modifier
      </button>
    </div>
    <p class="text-muted text-sm mb-6">Modifiez les informations générales de votre entreprise.</p>

    <div class="flex flex-col md:flex-row gap-8">
      <!-- Logo -->
      <div class="flex flex-col items-center gap-3 shrink-0">
        <div class="w-28 h-28 rounded-xl border border-border bg-background flex items-center justify-center">
          <Building2 class="w-10 h-10 text-primary-light" />
        </div>
        <button class="bg-background border border-border rounded-lg px-3 py-1.5 text-sm text-foreground hover:border-primary/40 transition-colors whitespace-nowrap">
          Changer le logo
        </button>
        <p class="text-muted text-xs text-center">PNG, JPG ou SVG. Max 2MB.</p>
      </div>

      <!-- Détails -->
      <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
        <div class="flex justify-between sm:block">
          <span class="text-muted text-sm">Nom de l'entreprise</span>
          <p class="text-foreground text-sm sm:mt-1">Nathan Company</p>
        </div>
        <div class="flex justify-between sm:block">
          <span class="text-muted text-sm">Référence</span>
          <p class="text-foreground text-sm sm:mt-1">ENT-000003</p>
        </div>
        <div class="flex justify-between sm:block">
          <span class="text-muted text-sm">Téléphone</span>
          <p class="text-foreground text-sm sm:mt-1">+228 90 12 34 56</p>
        </div>
        <div class="flex justify-between sm:block">
          <span class="text-muted text-sm">Email</span>
          <p class="text-foreground text-sm sm:mt-1">contact@nathancompany.com</p>
        </div>
        <div class="flex justify-between sm:block">
          <span class="text-muted text-sm">Site web</span>
          <a href="https://nathancompany.com" class="text-primary-light text-sm sm:mt-1 block hover:underline">
            https://nathancompany.com
          </a>
        </div>
        <div class="flex justify-between sm:block">
          <span class="text-muted text-sm">Adresse</span>
          <p class="text-foreground text-sm sm:mt-1">123 Rue de la Paix, Lomé, Togo</p>
        </div>
        <div class="flex justify-between sm:block">
          <span class="text-muted text-sm">Statut</span>
          <span class="inline-block mt-0.5 sm:mt-1 text-xs font-medium text-green-500 bg-green-500/10 px-2 py-0.5 rounded-full">
            Active
          </span>
        </div>
        <div class="flex justify-between sm:block">
          <span class="text-muted text-sm">Fuseau horaire</span>
          <p class="text-foreground text-sm sm:mt-1">Africa/Lome (UTC+00:00)</p>
        </div>
      </div>
    </div>
  </div>
</template>