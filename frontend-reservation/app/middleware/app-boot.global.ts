// Pages sans middleware auth/guest (accueil publique, liens d'action envoyés par email) :
// rien à vérifier, on lève immédiatement le cache plein écran du tout premier chargement
// (cf. useAppBooting). Les routes protégées lèvent ce même flag elles-mêmes une fois leur
// propre vérification terminée (middleware/auth.ts, middleware/guest.ts).
const GATED_MIDDLEWARES = ['auth', 'guest']

export default defineNuxtRouteMiddleware((to) => {
  if (import.meta.server) return

  const middleware = to.meta.middleware
  const isGated = Array.isArray(middleware)
    ? middleware.some(m => GATED_MIDDLEWARES.includes(m))
    : GATED_MIDDLEWARES.includes(middleware as string)

  if (!isGated) {
    useAppBooting().value = false
  }
})
