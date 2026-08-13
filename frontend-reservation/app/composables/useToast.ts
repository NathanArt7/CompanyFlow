export interface ToastItem {
  id: string
  type: 'success' | 'error'
  message: string
}

const TOAST_DURATION_MS = 4000

// useState (et non un simple ref) : même raison que les autres composables d'état global
// de ce projet (cf. useApi.ts, usePageLoading.ts) — une seule liste de toasts partagée par
// toute l'app, quel que soit le composant qui la déclenche.
function useToastState() {
  return useState<ToastItem[]>('toasts', () => [])
}

export function useToast() {
  const toasts = useToastState()

  function dismiss(id: string) {
    toasts.value = toasts.value.filter(t => t.id !== id)
  }

  function push(type: ToastItem['type'], message: string) {
    const id = `${Date.now()}-${Math.random()}`
    toasts.value.push({ id, type, message })
    setTimeout(() => dismiss(id), TOAST_DURATION_MS)
  }

  return {
    toasts,
    success: (message: string) => push('success', message),
    error: (message: string) => push('error', message),
    dismiss,
  }
}
