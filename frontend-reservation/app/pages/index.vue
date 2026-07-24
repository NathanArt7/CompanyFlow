<!-- app/pages/index.vue -->
<script setup lang="ts">
definePageMeta({ layout: 'marketing' })
</script>

<template>
  <MarketingNavbar />
  <MarketingHeroSection />
  <MarketingDashboardPreview />
  <MarketingFeaturesGrid />
  <MarketingOnboardingSteps />
  <MarketingCallToAction />
  <MarketingFooter />
</template>