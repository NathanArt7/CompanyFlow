<script setup lang="ts">
const route = useRoute()

definePageMeta({ layout: 'marketing' })
</script>

<template>
  <SetPasswordForm :token="route.params.token as string" />
</template>