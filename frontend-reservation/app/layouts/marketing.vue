<template>
  <div class="marketing-dark min-h-screen bg-background text-foreground">
    <slot />
  </div>
</template>

<style scoped>
/* La page marketing reste toujours en thème sombre, quel que soit le choix clair/sombre
   fait dans l'application : on réaffirme les valeurs sombres localement, ce qui prend le
   pas sur la classe .light posée sur <html> par le toggle de thème. */
.marketing-dark {
  --color-background: #0a0a0f;
  --color-surface: #111117;
  --color-border: #1f1f28;
  --color-muted: #9ca3af;
  --color-foreground: #f5f5f7;
  --color-skeleton: #23232e;
  --color-grid-line: rgba(255, 255, 255, 0.22);
}
</style>