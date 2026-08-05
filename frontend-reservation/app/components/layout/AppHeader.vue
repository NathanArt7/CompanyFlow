<script setup lang="ts">
import { Bell, Pencil, LogOut, Menu, ChevronDown } from 'lucide-vue-next'
import { computed, ref, onMounted, onUnmounted } from 'vue'

const authStore = useAuthStore()
const companyStore = useCompanyStore()
const notificationStore = useNotificationStore()
const { toggleMobile } = useSidebar()

const companyName = computed(() => companyStore.nom ?? 'CompanyPilot')

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

const config = useRuntimeConfig()
const photoUrl = computed(() => {
  const photo = authStore.user?.photo
  if (!photo) return null
  return `${config.public.apiBase.replace(/\/api\/?$/, '')}/storage/${photo}`
})

const isMenuOpen = ref(false)
const isNotificationsOpen = ref(false)
const isProfileModalOpen = ref(false)
const isLogoutConfirmOpen = ref(false)
const isLoggingOut = ref(false)

function closeMenu() {
  isMenuOpen.value = false
  isNotificationsOpen.value = false
}

function toggleNotifications() {
  isNotificationsOpen.value = !isNotificationsOpen.value
  isMenuOpen.value = false
}

function openProfileModal() {
  isProfileModalOpen.value = true
  closeMenu()
}

function requestLogout() {
  isLogoutConfirmOpen.value = true
  closeMenu()
}

async function confirmLogout() {
  isLoggingOut.value = true
  await authStore.logout()
  isLoggingOut.value = false
  isLogoutConfirmOpen.value = false
}

let unreadPollTimer: ReturnType<typeof setInterval> | undefined

onMounted(() => {
  document.addEventListener('click', closeMenu)
  notificationStore.fetchUnreadCount()
  unreadPollTimer = setInterval(() => notificationStore.fetchUnreadCount(), 60000)
})

onUnmounted(() => {
  document.removeEventListener('click', closeMenu)
  clearInterval(unreadPollTimer)
})
</script>

<template>
  <header class="h-16 border-b border-border flex items-center justify-between px-4 sm:px-6 shrink-0">
    <div class="flex items-center gap-3 min-w-0">
      <button
        class="lg:hidden text-muted hover:text-foreground transition-colors shrink-0"
        title="Ouvrir le menu"
        @click="toggleMobile"
      >
        <Menu class="w-5 h-5" />
      </button>
      <span class="text-foreground text-lg sm:text-2xl font-medium truncate">
        {{ companyName }}
      </span>
    </div>

    <div class="flex items-center gap-5">
      <ThemeToggle />

      <div class="relative" @click.stop>
        <button class="relative text-muted hover:text-foreground transition-colors" @click="toggleNotifications">
          <Bell class="w-5 h-5" />
          <span
            v-if="notificationStore.unreadCount > 0"
            class="absolute -top-1.5 -right-1.5 min-w-[16px] h-4 px-1 rounded-full bg-red-500 text-white text-[10px] leading-4 font-medium text-center"
          >
            {{ notificationStore.unreadCount > 9 ? '9+' : notificationStore.unreadCount }}
          </span>
        </button>

        <NotificationDropdown v-if="isNotificationsOpen" />
      </div>

      <div class="relative" @click.stop>
        <button class="flex items-center gap-2.5" @click="isMenuOpen = !isMenuOpen; isNotificationsOpen = false">
          <img v-if="photoUrl" :src="photoUrl" alt="" class="w-8 h-8 rounded-full object-cover">
          <div v-else class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center">
            <span class="text-primary-light text-xs font-semibold">{{ initials }}</span>
          </div>
          <div class="text-left hidden sm:block">
            <p class="text-foreground text-sm font-medium leading-tight">{{ fullName }}</p>
            <p class="text-muted text-xs leading-tight">{{ roleLabel }}</p>
          </div>
          <ChevronDown class="w-4 h-4 text-muted hidden sm:block" />
        </button>

        <div
          v-if="isMenuOpen"
          class="absolute right-0 top-full mt-2 w-48 bg-surface border border-border rounded-lg shadow-lg overflow-hidden z-10"
        >
          <button
            class="w-full flex items-center gap-2 px-3 py-2.5 text-sm text-foreground hover:bg-border/50 transition-colors"
            @click="openProfileModal"
          >
            <Pencil class="w-3.5 h-3.5" />
            Modifier le profil
          </button>
          <button
            class="w-full flex items-center gap-2 px-3 py-2.5 text-sm text-red-400 hover:bg-border/50 transition-colors"
            @click="requestLogout"
          >
            <LogOut class="w-3.5 h-3.5" />
            Se déconnecter
          </button>
        </div>
      </div>
    </div>

    <EditProfileModal
      :is-open="isProfileModalOpen"
      @close="isProfileModalOpen = false"
    />

    <ConfirmDialog
      :is-open="isLogoutConfirmOpen"
      title="Se déconnecter ?"
      message="Vous devrez vous reconnecter pour accéder à votre compte."
      confirm-label="Se déconnecter"
      :is-loading="isLoggingOut"
      @cancel="isLogoutConfirmOpen = false"
      @confirm="confirmLogout"
    />
  </header>
</template>
