<template>
  <div class="min-h-screen bg-background text-foreground flex">
    <!-- Sidebar viendra ici une fois créée -->
    <!-- <AppSidebar /> -->

    <div class="flex-1 flex flex-col">
      <!-- Header viendra ici une fois créé -->
      <!-- <AppHeader /> -->

      <main class="flex-1 p-6">
        <slot />
      </main>
    </div>
  </div>
</template>