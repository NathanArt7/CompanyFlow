<script setup lang="ts">
definePageMeta({ layout: 'default', middleware: 'auth' })
</script>

<template>
  <div class="space-y-6">
    <div>
      <h1 class="text-foreground text-2xl md:text-3xl font-bold">Paramètres</h1>
      <p class="text-muted text-sm mt-1">Gérez les paramètres généraux de votre application.</p>
    </div>

    <div class="space-y-6">
      <CompanyInfoCard />

      <BusinessHoursCard />

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <AboutCard />
        <PasswordCard />
      </div>
    </div>
  </div>
</template>
