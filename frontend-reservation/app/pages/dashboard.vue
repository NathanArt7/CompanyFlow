<script setup lang="ts">
definePageMeta({ layout: 'default' })

const userName = 'Nathan'
</script>

<template>
  <div class="space-y-6">
    <DashboardHeader :user-name="userName" />
    <StatsGrid />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Colonne principale (2/3) -->
      <div class="lg:col-span-2 space-y-6">
        <UpcomingReservations />

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
          <RoomOccupancy />
          <TodoWidget />
        </div>
      </div>

      <!-- Colonne latérale (1/3) -->
      <div class="space-y-6">
        <CalendarWidget />
        <RecentActivity />
      </div>
    </div>
  </div>
</template>