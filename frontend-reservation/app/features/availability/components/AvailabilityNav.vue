<script setup lang="ts">
import { ChevronLeft, ChevronRight, Calendar } from 'lucide-vue-next'
import { ref } from 'vue'

const weekLabel = ref('19 – 25 mai 2026')
const selectedRoom = ref('all')
const selectedType = ref('all')
</script>

<template>
  <div class="bg-surface border border-border rounded-xl p-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
    <div class="flex items-center gap-3 flex-wrap">
      <button class="w-9 h-9 flex items-center justify-center rounded-lg border border-border text-muted hover:text-foreground hover:bg-border/50 transition-colors">
        <ChevronLeft class="w-4 h-4" />
      </button>
      <button class="bg-background border border-border rounded-lg px-4 py-2 text-sm font-medium text-foreground hover:border-primary/40 transition-colors">
        Aujourd'hui
      </button>
      <button class="w-9 h-9 flex items-center justify-center rounded-lg border border-border text-muted hover:text-foreground hover:bg-border/50 transition-colors">
        <ChevronRight class="w-4 h-4" />
      </button>

      <div class="flex items-center gap-2 text-foreground text-lg font-semibold ml-2">
        {{ weekLabel }}
        <Calendar class="w-4 h-4 text-muted" />
      </div>
    </div>

    <div class="flex items-center gap-3">
      <select
        v-model="selectedRoom"
        class="bg-background border border-border rounded-lg px-3 py-2 text-sm text-foreground focus:outline-none focus:border-primary transition-colors"
      >
        <option value="all">Toutes les salles</option>
        <option value="a">Salle de réunion A</option>
        <option value="b">Salle de réunion B</option>
        <option value="conf">Salle de conférence</option>
        <option value="formation">Salle de formation</option>
      </select>

      <select
        v-model="selectedType"
        class="bg-background border border-border rounded-lg px-3 py-2 text-sm text-foreground focus:outline-none focus:border-primary transition-colors"
      >
        <option value="all">Tous les types</option>
        <option value="reunion">Réunion</option>
        <option value="stockage">Stockage</option>
      </select>
    </div>
  </div>
</template>