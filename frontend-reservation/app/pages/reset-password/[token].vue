<script setup lang="ts">
const route = useRoute()

definePageMeta({ layout: 'marketing' })
</script>

<template>
  <ResetPasswordForm :token="route.params.token as string" />
</template>
