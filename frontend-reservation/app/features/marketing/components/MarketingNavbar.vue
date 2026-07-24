<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue'
import { Menu, X } from 'lucide-vue-next'

const isScrolled = ref(false)
const isMobileMenuOpen = ref(false)

const handleScroll = () => {
  isScrolled.value = window.scrollY > 20
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})

const scrollToSection = (id: string) => {
  isMobileMenuOpen.value = false
  const section = document.getElementById(id)

  if (!section) return

  section.scrollIntoView({
    behavior: 'smooth',
    block: 'start'
  })
}
</script>

<template>
  <header
    :class="[
      'fixed top-0 left-0 right-0 z-50 flex items-center justify-between px-6 md:px-8 lg:px-12 h-16 transition-all duration-300',
      isScrolled
        ? 'bg-black/70 backdrop-blur-xl border-b border-white/10 shadow-lg'
        : 'bg-transparent'
    ]"
  >
    <div class="flex items-center">
      <Logo />
    </div>

    <!-- Nav desktop -->
    <nav class="hidden md:flex items-center gap-12 text-base text-muted">
      <button
        @click="scrollToSection('features')"
        class="hover:text-foreground transition-colors"
      >
        Fonctionnalités
      </button>
      <a href="#works" class="hover:text-foreground transition-colors">Fonctionnement</a>
      <a href="#cta" class="hover:text-foreground transition-colors">Tarifs</a>
    </nav>

    <!-- Actions desktop -->
    <div class="hidden md:flex items-center gap-4">
      <NuxtLink to="/login" class="text-sm text-foreground hover:text-primary transition-colors">
        Se connecter
      </NuxtLink>
      <NuxtLink
        to="/signup"
        class="bg-primary hover:bg-primary-hover text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors"
      >
        S'inscrire
      </NuxtLink>
    </div>

    <!-- Bouton burger (mobile uniquement) -->
    <button
      class="md:hidden text-foreground"
      @click="isMobileMenuOpen = !isMobileMenuOpen"
    >
      <Menu v-if="!isMobileMenuOpen" class="w-6 h-6" />
      <X v-else class="w-6 h-6" />
    </button>

    <!-- Panneau mobile déroulant -->
    <div
      v-if="isMobileMenuOpen"
      class="md:hidden absolute top-16 left-0 right-0 bg-black/95 backdrop-blur-xl border-b border-white/10 px-6 py-6 flex flex-col gap-6"
    >
      <nav class="flex flex-col gap-4 text-base text-muted">
        <button
          @click="scrollToSection('features')"
          class="text-left hover:text-foreground transition-colors"
        >
          Fonctionnalités
        </button>
        <a href="#works" class="hover:text-foreground transition-colors" @click="isMobileMenuOpen = false">
          Fonctionnement
        </a>
        <a href="#cta" class="hover:text-foreground transition-colors" @click="isMobileMenuOpen = false">
          Tarifs
        </a>
      </nav>

      <div class="flex flex-col gap-3 pt-4 border-t border-white/10">
        <NuxtLink
          to="/login"
          class="text-sm text-foreground hover:text-primary transition-colors"
          @click="isMobileMenuOpen = false"
        >
          Se connecter
        </NuxtLink>
        <NuxtLink
          to="/signup"
          class="bg-primary hover:bg-primary-hover text-white text-sm font-medium px-4 py-2 rounded-lg text-center transition-colors"
          @click="isMobileMenuOpen = false"
        >
          S'inscrire
        </NuxtLink>
      </div>
    </div>
  </header>
</template>