export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',

  devtools: {
    enabled: false,
  },

  runtimeConfig: {
    public: {
      apiBase: 'http://localhost:8000/api',
    },
  },

  modules: [
    '@nuxtjs/tailwindcss',
    '@pinia/nuxt',
    '@vueuse/nuxt',
    '@nuxt/eslint',
  ],

   css: [
    '~/assets/css/main.css'
  ],

   app: {
    pageTransition: false,
    layoutTransition: false
  },

  router: {
    options: {
      scrollBehaviorType: 'smooth'
    }
  },

  components: [
    { path: '~/components', pathPrefix: false },
    { path: '~/features', pathPrefix: false },
  ],
})