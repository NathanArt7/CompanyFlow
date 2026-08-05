<template>
  <NuxtLayout>
    <NuxtPage />
  </NuxtLayout>
</template>

<script setup lang="ts">
const { init } = useTheme()
onMounted(init)

useHead({
  titleTemplate: 'CompanyPilot',
   link: [
    {
      rel: 'icon',
      type: 'image/svg+xml',
      href: '/images/logo/companyflow-logo.svg'
    }
  ]
})
</script>