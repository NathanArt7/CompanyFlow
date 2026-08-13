// Couvre tout l'écran (y compris sidebar/header) le temps de la toute première vérification
// d'authentification de l'onglet, avant qu'on sache si le visiteur a le droit de voir la
// structure de l'app (cf. middleware/auth.ts, middleware/guest.ts, middleware/app-boot.global.ts).
// Contrairement à usePageLoading, ce flag ne repasse jamais à true après : c'est un état
// "one-shot" pour toute la durée de vie de l'onglet.
export function useAppBooting() {
  return useState<boolean>('app-booting', () => true)
}
