<template>
  <header
    class="fixed top-0 left-0 right-0 z-40 transition-all duration-300"
    :class="scrolled ? 'bg-dark-950/95 backdrop-blur-md border-b border-dark-700' : 'bg-transparent'"
  >
    <nav class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between" aria-label="Navigation principale">
      <!-- Logo -->
      <router-link to="/" class="flex items-center gap-2.5" aria-label="FitReserve - Accueil">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" class="w-8 h-8 flex-shrink-0">
          <rect width="100" height="100" rx="22" fill="#111111"/>
          <polygon points="62,6 30,52 52,52 38,94 70,48 48,48" fill="#f97316"/>
        </svg>
        <div class="flex items-center gap-0.5">
          <span class="font-display font-black text-2xl uppercase tracking-tighter text-white">FIT</span>
          <span class="font-display font-black text-2xl uppercase tracking-tighter text-orange-500">RESERVE</span>
        </div>
      </router-link>

      <!-- Desktop nav -->
      <div class="hidden md:flex items-center gap-8">
        <router-link
          v-for="link in navLinks"
          :key="link.to"
          :to="link.to"
          class="text-sm font-bold uppercase tracking-widest text-zinc-400 hover:text-white transition-colors duration-200"
          active-class="text-orange-500"
        >
          {{ link.label }}
        </router-link>
      </div>

      <!-- Auth buttons -->
      <div class="hidden md:flex items-center gap-3">
        <template v-if="auth.isAuthenticated">
          <span class="text-sm text-zinc-500 font-medium hidden lg:block">{{ auth.fullName }}</span>
          <router-link v-if="!auth.isAdmin" to="/profil" class="btn-ghost text-xs py-2 px-4">Mon profil</router-link>
          <router-link v-if="auth.isAdmin" to="/admin" class="btn-ghost text-xs py-2 px-4">Admin</router-link>
          <button @click="handleLogout" class="btn-ghost text-xs py-2 px-4">Déconnexion</button>
        </template>
        <template v-else>
          <router-link to="/login" class="btn-ghost text-xs py-2 px-4">Connexion</router-link>
          <router-link to="/register" class="btn-primary text-xs py-2 px-5">
            <span>Commencer</span>
          </router-link>
        </template>
      </div>

      <!-- Mobile menu button -->
      <button
        @click="mobileOpen = !mobileOpen"
        class="md:hidden w-9 h-9 flex flex-col items-center justify-center gap-1.5"
        :aria-expanded="mobileOpen"
        aria-label="Menu"
      >
        <span class="block w-6 h-0.5 bg-white transition-all duration-200" :class="mobileOpen ? 'rotate-45 translate-y-2' : ''"></span>
        <span class="block w-6 h-0.5 bg-white transition-all duration-200" :class="mobileOpen ? 'opacity-0' : ''"></span>
        <span class="block w-6 h-0.5 bg-white transition-all duration-200" :class="mobileOpen ? '-rotate-45 -translate-y-2' : ''"></span>
      </button>
    </nav>

    <!-- Mobile menu -->
    <transition name="slide-down">
      <div v-if="mobileOpen" class="md:hidden bg-dark-950 border-t border-dark-700 px-4 pb-6">
        <div class="flex flex-col gap-1 pt-4">
          <router-link
            v-for="link in navLinks"
            :key="link.to"
            :to="link.to"
            class="py-3 text-sm font-bold uppercase tracking-widest text-zinc-400 hover:text-orange-500 border-b border-dark-700 transition-colors"
            active-class="text-orange-500"
            @click="mobileOpen = false"
          >
            {{ link.label }}
          </router-link>
          <div class="flex flex-col gap-3 pt-4">
            <template v-if="auth.isAuthenticated">
              <router-link v-if="!auth.isAdmin" to="/profil" class="btn-ghost text-xs text-center" @click="mobileOpen = false">Mon profil</router-link>
              <router-link v-if="auth.isAdmin" to="/admin" class="btn-ghost text-xs text-center" @click="mobileOpen = false">Admin</router-link>
              <button @click="handleLogout" class="btn-ghost text-xs">Déconnexion</button>
            </template>
            <template v-else>
              <router-link to="/login" class="btn-ghost text-xs text-center" @click="mobileOpen = false">Connexion</router-link>
              <router-link to="/register" class="btn-primary text-xs text-center" @click="mobileOpen = false">Commencer</router-link>
            </template>
          </div>
        </div>
      </div>
    </transition>
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const router = useRouter()
const mobileOpen = ref(false)
const scrolled = ref(false)

const navLinks = computed(() => {
  const links = [{ to: '/seances', label: 'Séances' }]
  if (auth.isAuthenticated && !auth.isAdmin) links.push({ to: '/reservations', label: 'Mes Réservations' })
  return links
})

function handleLogout() {
  auth.logout()
  mobileOpen.value = false
  router.push('/')
}

function onScroll() { scrolled.value = window.scrollY > 20 }
onMounted(() => window.addEventListener('scroll', onScroll))
onUnmounted(() => window.removeEventListener('scroll', onScroll))
</script>

<style scoped>
.slide-down-enter-active, .slide-down-leave-active { transition: all 0.2s ease; }
.slide-down-enter-from, .slide-down-leave-to { opacity: 0; transform: translateY(-8px); }
</style>
