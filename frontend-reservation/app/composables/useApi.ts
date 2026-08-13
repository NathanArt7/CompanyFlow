import { FetchError } from 'ofetch'

export interface ApiError {
  status: number
  message: string
  errors?: Record<string, string[]>
  code?: string
}

function normalizeError(error: unknown): ApiError {
  if (error instanceof FetchError) {
    const data = error.data as { message?: string; errors?: Record<string, string[]>; code?: string } | undefined
    const status = error.response?.status ?? error.statusCode ?? 0
    const errors = data?.errors
    const firstFieldError = errors ? Object.values(errors)[0]?.[0] : undefined

    return {
      status,
      errors,
      code: data?.code,
      message: firstFieldError ?? data?.message ?? 'Une erreur est survenue. Veuillez réessayer.',
    }
  }

  return {
    status: 0,
    message: 'Impossible de contacter le serveur. Vérifiez votre connexion.',
  }
}

const TOKEN_STORAGE_KEY = 'auth_token'

// sessionStorage (et non un cookie) : propre à chaque onglet, ce qui permet d'être connecté
// à des comptes différents dans deux onglets du même navigateur. Contrepartie : le serveur
// (SSR) n'a plus aucun accès au token, sessionStorage n'existant que côté navigateur — c'est
// pour ça que les middlewares auth.ts / guest.ts s'exécutent désormais uniquement côté client
// (cf. useAppBooting, qui masque toute la page le temps de cette vérification cliente).
function useAuthToken() {
  const token = useState<string | null>('auth-token', () => null)

  // Le payload SSR ne contient jamais le token (il est toujours null côté serveur) : on
  // l'hydrate depuis sessionStorage au premier accès côté client.
  if (import.meta.client && token.value === null) {
    token.value = sessionStorage.getItem(TOKEN_STORAGE_KEY)
  }

  function setToken(value: string | null) {
    token.value = value
    if (import.meta.client) {
      if (value) sessionStorage.setItem(TOKEN_STORAGE_KEY, value)
      else sessionStorage.removeItem(TOKEN_STORAGE_KEY)
    }
  }

  return { token, setToken }
}

// Compteur de requêtes en cours partagé par toute l'app : permet au loader de page
// (app.vue) de savoir quand les données réellement affichées (pas juste le
// changement de route) sont arrivées. { silent: true } exclut un appel de ce
// compteur (ex. le polling en arrière-plan des notifications, qui ne doit jamais
// redéclencher le loader plein écran).
export function usePendingRequestCount() {
  return useState<number>('pending-request-count', () => 0)
}

export function useApi() {
  const config = useRuntimeConfig()
  const { token: authToken, setToken } = useAuthToken()
  const pendingCount = usePendingRequestCount()

  async function apiFetch<T>(path: string, options: Record<string, unknown> = {}): Promise<T> {
    const { silent, ...fetchOptions } = options

    if (!silent) pendingCount.value++

    try {
      return await $fetch<T>(path, {
        baseURL: config.public.apiBase,
        ...fetchOptions,
        headers: {
          Accept: 'application/json',
          ...(fetchOptions.headers as Record<string, string> | undefined),
          ...(authToken.value ? { Authorization: `Bearer ${authToken.value}` } : {}),
        },
      })
    } catch (error) {
      const normalized = normalizeError(error)

      if (normalized.status === 401) {
        setToken(null)
        const authStore = useAuthStore()
        authStore.user = null
      }

      throw normalized
    } finally {
      if (!silent) pendingCount.value--
    }
  }

  return { apiFetch, authToken, setToken }
}
