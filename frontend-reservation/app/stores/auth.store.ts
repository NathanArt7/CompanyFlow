import { useAuthService } from '~/features/auth/services/auth.service'
import type { MeResponse } from '~/features/auth/type'

export const useAuthStore = defineStore('auth', () => {
  const user = ref<MeResponse | null>(null)

  const isAuthenticated = computed(() => !!user.value)
  const permissions = computed(() => user.value?.permissions ?? [])

  async function login(email: string, password: string) {
    const authService = useAuthService()
    const { setToken } = useApi()

    const response = await authService.login(email, password)
    setToken(response.token)

    await fetchMe()
  }

  async function fetchMe() {
    const authService = useAuthService()
    user.value = await authService.me()
  }

  async function logout() {
    const authService = useAuthService()
    const { setToken } = useApi()

    try {
      await authService.logout()
    } catch {
      // best-effort: on nettoie la session locale même si l'appel échoue
    }

    setToken(null)
    user.value = null
    await navigateTo('/login')
  }

  return { user, isAuthenticated, permissions, login, fetchMe, logout }
})
