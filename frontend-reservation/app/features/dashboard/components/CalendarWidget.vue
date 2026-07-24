<template>
  <div class="bg-surface border border-border rounded-xl p-5">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-foreground font-semibold">Calendrier</h2>
      <NuxtLink
        to="/availability"
        class="text-xs bg-background border border-border rounded-lg px-3 py-1.5 text-foreground hover:border-primary/40 transition-colors"
      >
        Voir le calendrier
      </NuxtLink>
    </div>

    <MiniCalendar />
  </div>
</template>