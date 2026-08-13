// useState (et non un simple ref) : garantit une instance unique partagée par toute
// l'app, comme les autres composables d'état global de ce projet (cf. useApi.ts).
// Valeur initiale à true : au premier chargement (F5), le loader doit déjà être visible
// dès le montage client, avant même que app.vue n'ait eu la main pour le déclencher.
export function usePageLoading() {
  return useState<boolean>('page-loading', () => true)
}
