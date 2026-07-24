<script setup lang="ts">
import { Search, Bell, HelpCircle, ChevronDown } from 'lucide-vue-next'
</script>

<template>
  <header class="h-16 border-b border-border flex items-center justify-between px-6 shrink-0">
    <!-- Recherche -->
    <div class="relative w-full max-w-sm">
      <Search class="w-4 h-4 text-muted absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
      <input
        type="text"
        placeholder="Rechercher..."
        class="w-full bg-background border border-border rounded-lg pl-10 pr-14 py-2 text-sm text-foreground placeholder:text-muted/60 focus:outline-none focus:border-primary transition-colors"
      >
      <kbd class="absolute right-3 top-1/2 -translate-y-1/2 text-muted text-xs bg-surface border border-border rounded px-1.5 py-0.5">
        ⌘K
      </kbd>
    </div>

    <!-- Actions -->
    <div class="flex items-center gap-5">
      <button class="relative text-muted hover:text-foreground transition-colors">
        <Bell class="w-5 h-5" />
        <span class="absolute -top-0.5 -right-0.5 w-2 h-2 rounded-full bg-primary" />
      </button>

      <button class="text-muted hover:text-foreground transition-colors">
        <HelpCircle class="w-5 h-5" />
      </button>

      <button class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center">
          <span class="text-primary-light text-xs font-semibold">NA</span>
        </div>
        <div class="text-left hidden sm:block">
          <p class="text-foreground text-sm font-medium leading-tight">Nathan Art</p>
          <p class="text-muted text-xs leading-tight">Super Administrateur</p>
        </div>
        <ChevronDown class="w-4 h-4 text-muted" />
      </button>
    </div>
  </header>
</template>