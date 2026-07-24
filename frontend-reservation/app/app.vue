<template>
  <NuxtLayout>
    <NuxtPage />
  </NuxtLayout>
</template>

<script setup lang="ts">
useHead({
  titleTemplate: 'CompanyFlow',
   link: [
    {
      rel: 'icon',
      type: 'image/svg+xml',
      href: '/images/logo/companyflow-logo.svg'
    }
  ]
})
</script>