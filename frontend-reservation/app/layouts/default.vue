<template>
  <div class="h-screen bg-background text-foreground flex">
    <AppSidebar />

    <div class="flex-1 flex flex-col min-w-0 min-h-0">
      <AppHeader />

      <main class="relative flex-1 min-h-0 overflow-hidden">
        <div ref="scrollAreaRef" class="h-full overflow-y-auto p-4 sm:p-6 [scrollbar-gutter:stable]">
          <slot />
        </div>
        <PageLoader />
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
// Le wrapper scrollable vit dans ce layout, donc il persiste (et garde sa
// position de scroll) d'une page à l'autre : on la remet à zéro à chaque
// changement de route pour retrouver le comportement attendu d'une nouvelle page.
const scrollAreaRef = ref<HTMLElement | null>(null)
const route = useRoute()

watch(
  () => route.path,
  () => { scrollAreaRef.value?.scrollTo({ top: 0 }) },
)
</script>