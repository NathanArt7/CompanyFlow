<script setup lang="ts">
definePageMeta({ layout: 'marketing' })

const router = useRouter()

const goBack = () => {
  router.push('/onboarding/company')
}
</script>

<template>
  <ReservationSettingsForm @back="goBack" />
</template>