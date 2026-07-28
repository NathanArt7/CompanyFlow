<script setup lang="ts">
import { Bell } from 'lucide-vue-next'
import { computed } from 'vue'

const authStore = useAuthStore()
const companyStore = useCompanyStore()

// Pas d'endpoint GET /entreprise pour re-fetch après un reload sur une autre session :
// on retombe sur le nom de l'utilisateur connecté tant que ce gap n'est pas comblé côté backend.
const companyName = computed(() => companyStore.nom ?? authStore.user?.nom ?? 'CompanyFlow')

const fullName = computed(() => {
  const user = authStore.user
  return user ? `${user.prenom} ${user.nom}` : ''
})

const initials = computed(() => {
  const user = authStore.user
  if (!user) return ''
  return `${user.prenom.charAt(0)}${user.nom.charAt(0)}`.toUpperCase()
})

const roleLabel = computed(() => authStore.user?.role ?? '')
</script>

<template>
  <header class="h-16 border-b border-border flex items-center justify-between px-6 shrink-0">
    <span class="text-foreground text-2xl font-medium truncate">
      {{ companyName }}
    </span> 

    <div class="flex items-center gap-5">
      <button class="relative text-muted hover:text-foreground transition-colors">
        <Bell class="w-5 h-5" />
        <span class="absolute -top-0.5 -right-0.5 w-2 h-2 rounded-full bg-primary" />
      </button>

      <button class="flex items-center gap-2.5" title="Se déconnecter" @click="authStore.logout()">
        <div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center">
          <span class="text-primary-light text-xs font-semibold">{{ initials }}</span>
        </div>
        <div class="text-left hidden sm:block">
          <p class="text-foreground text-sm font-medium leading-tight">{{ fullName }}</p>
          <p class="text-muted text-xs leading-tight">{{ roleLabel }}</p>
        </div>
      </button>
    </div>
  </header>
</template>