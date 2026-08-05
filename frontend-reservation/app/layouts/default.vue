<template>
  <div class="min-h-screen bg-background text-foreground flex">
    <AppSidebar />

    <div class="flex-1 flex flex-col min-w-0">
      <AppHeader />

      <main class="flex-1 p-4 sm:p-6 overflow-y-auto">
        <slot />
      </main>
    </div>
  </div>
</template>