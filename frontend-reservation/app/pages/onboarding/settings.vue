<script setup lang="ts">
definePageMeta({ layout: 'marketing', middleware: 'auth' })

const router = useRouter()

const goBack = () => {
  router.push('/onboarding/company')
}
</script>

<template>
  <ReservationSettingsForm @back="goBack" />
</template>