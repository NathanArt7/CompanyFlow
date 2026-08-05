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

// useCookie() ne partage pas la même ref réactive entre plusieurs appels indépendants :
// écrire le token puis le relire immédiatement (ex. juste après le login) peut lire une
// valeur pas encore synchronisée. useState() garantit une ref unique partagée au sein de
// l'instance de l'app ; le cookie ne sert qu'à faire persister le token entre les rechargements.
function useAuthToken() {
  // secure: désactivé en dev (process.dev) car le serveur local tourne en http:// —
  // un cookie "secure" y serait silencieusement ignoré par le navigateur, cassant la
  // connexion. sameSite: 'strict' limite l'envoi du cookie aux requêtes same-site,
  // réduisant l'exposition du token en cas de faille XSS ailleurs dans l'app (un vrai
  // cookie httpOnly serait préférable mais demanderait que ce soit le backend Laravel,
  // et non ce composable côté client, qui pose le cookie via l'en-tête Set-Cookie).
  const cookie = useCookie<string | null>('auth_token', {
    default: () => null,
    secure: !process.dev,
    sameSite: 'strict',
  })
  const token = useState<string | null>('auth-token', () => cookie.value)

  function setToken(value: string | null) {
    token.value = value
    cookie.value = value
  }

  return { token, setToken }
}

export function useApi() {
  const config = useRuntimeConfig()
  const { token: authToken, setToken } = useAuthToken()

  async function apiFetch<T>(path: string, options: Record<string, unknown> = {}): Promise<T> {
    try {
      return await $fetch<T>(path, {
        baseURL: config.public.apiBase,
        ...options,
        headers: {
          Accept: 'application/json',
          ...(options.headers as Record<string, string> | undefined),
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
    }
  }

  return { apiFetch, authToken, setToken }
}
