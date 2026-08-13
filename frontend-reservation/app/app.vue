<template>
  <div>
    <AppBootLoader />
    <ToastContainer />
    <NuxtLayout>
      <NuxtPage />
    </NuxtLayout>
  </div>
</template>

<script setup lang="ts">
const { init } = useTheme()
onMounted(init)

// Anime pendant toute navigation entre pages ET tant que les données de la nouvelle
// page n'ont pas fini de charger (pas juste le temps du changement de route) : sinon
// le loader disparaît avant que les cards n'aient reçu leurs données. Le rendu du
// loader lui-même vit dans chaque layout (pas ici) pour ne recouvrir que la zone de
// contenu, jamais la sidebar/le header.
const isLoading = usePageLoading()
const pendingRequests = usePendingRequestCount()
const router = useRouter()

const MIN_VISIBLE_MS = 400
// Garde-fou : si des données ne finissent jamais de charger (requête bloquée, bug),
// le loader ne doit jamais rester affiché indéfiniment.
const MAX_VISIBLE_MS = 5000

let loadingStartedAt = 0
let hideTimer: ReturnType<typeof setTimeout> | undefined
let safetyTimer: ReturnType<typeof setTimeout> | undefined
let stopPendingWatch: (() => void) | undefined

function hideAfterMinDuration() {
  // Les données sont là : on n'a plus besoin du filet de sécurité, sinon il se
  // déclenche quand même plus tard et referme inutilement un loader déjà masqué.
  clearTimeout(safetyTimer)
  const remaining = MIN_VISIBLE_MS - (Date.now() - loadingStartedAt)
  hideTimer = setTimeout(() => {
    isLoading.value = false
  }, Math.max(0, remaining))
}

// Partagée par la navigation SPA (afterEach) ET le tout premier chargement (onMounted) :
// nextTick laisse le onMounted de la page en cours déclencher ses appels API avant de
// vérifier s'il y a des données en cours de chargement à attendre.
function waitForPageReady() {
  clearTimeout(hideTimer)
  clearTimeout(safetyTimer)
  stopPendingWatch?.()
  safetyTimer = setTimeout(() => {
    stopPendingWatch?.()
    isLoading.value = false
  }, MAX_VISIBLE_MS)

  nextTick(() => {
    if (pendingRequests.value > 0) {
      stopPendingWatch = watch(pendingRequests, (count) => {
        if (count === 0) {
          stopPendingWatch?.()
          hideAfterMinDuration()
        }
      })
    } else {
      hideAfterMinDuration()
    }
  })
}

router.beforeEach(() => {
  clearTimeout(hideTimer)
  clearTimeout(safetyTimer)
  stopPendingWatch?.()

  loadingStartedAt = Date.now()
  isLoading.value = true
})

router.afterEach(() => {
  waitForPageReady()
})

onMounted(() => {
  // Premier chargement (F5) : isLoading démarre déjà à true (cf. usePageLoading), on
  // attend juste que les données de la page initiale arrivent avant de le masquer.
  loadingStartedAt = Date.now()
  waitForPageReady()
})

useHead({
  titleTemplate: 'CompanyPilot',
   link: [
    {
      rel: 'icon',
      type: 'image/svg+xml',
      href: '/images/logo/companyflow-logo.svg'
    }
  ]
})
</script>