<template>
  <div class="page-container max-w-5xl mx-auto">

    <!-- Header -->
    <div class="mb-10 animate-fade-in">
      <p class="label-tag text-orange-500 mb-2">Disponibilités</p>
      <h1 class="section-title text-white">Planning <span class="text-gradient">de Marc</span></h1>
      <p class="text-zinc-500 mt-3 text-sm">Choisissez un créneau et réservez votre séance avec Marc.</p>
    </div>

    <!-- Loading -->
    <div v-if="seancesStore.loading" class="space-y-4">
      <div v-for="i in 4" :key="i" class="skeleton h-32"></div>
    </div>

    <!-- Error -->
    <div v-else-if="seancesStore.error" class="glass-card p-10 text-center" role="alert">
      <p class="text-red-400 font-bold mb-4">{{ seancesStore.error }}</p>
      <button @click="seancesStore.fetchSeances()" class="btn-ghost text-sm">Réessayer</button>
    </div>

    <!-- Planning groupé par semaine -->
    <div v-else class="space-y-10">
      <div v-for="(week, wi) in groupedByWeek" :key="wi">

        <!-- Label semaine -->
        <p class="label-tag text-zinc-600 mb-4">
          Semaine du {{ week.label }}
        </p>

        <!-- Jours de la semaine -->
        <div class="space-y-2">
          <div v-for="day in week.days" :key="day.date">

            <!-- Header jour -->
            <div class="flex items-center gap-3 mb-1">
              <p class="font-display font-black text-white uppercase text-base">{{ day.label }}</p>
              <div class="flex-1 h-px bg-dark-700"></div>
              <p class="text-zinc-600 text-xs">{{ day.available }} créneau{{ day.available > 1 ? 'x' : '' }} libre{{ day.available > 1 ? 's' : '' }}</p>
            </div>

            <!-- Créneaux du jour -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-2 mb-4">
              <button
                v-for="seance in day.seances"
                :key="seance.id"
                @click="handleSlotClick(seance)"
                :disabled="isBooked(seance.id) ? false : seance.placesRestantes === 0"
                class="relative p-3 text-center transition-all duration-200 border"
                :class="slotClass(seance)"
              >
                <p class="font-display font-black text-lg leading-none">{{ formatTime(seance.dateHeure) }}</p>
                <p class="text-xs mt-1 font-medium" :class="isBooked(seance.id) ? 'text-emerald-400' : !seance.disponible ? 'text-zinc-600' : seance.placesRestantes === 0 ? 'text-red-500' : 'text-zinc-500'">
                  {{ isBooked(seance.id) ? 'Réservé' : !seance.disponible ? 'Non disponible' : seance.placesRestantes === 0 ? 'Complet' : '1h — Disponible' }}
                </p>
                <!-- Loading indicator -->
                <div v-if="loadingId === seance.id" class="absolute inset-0 flex items-center justify-center bg-dark-900/70">
                  <LoadingSpinner size="sm" />
                </div>
              </button>
            </div>

          </div>
        </div>
      </div>

      <p v-if="groupedByWeek.length === 0" class="glass-card p-16 text-center text-zinc-500">
        Aucun créneau disponible pour le moment.
      </p>
    </div>

    <!-- Bottom CTA si non connecté -->
    <div v-if="!auth.isAuthenticated" class="mt-12 p-6 bg-dark-800 border border-orange-500/20 text-center">
      <p class="text-white font-bold mb-1">Prêt à réserver ?</p>
      <p class="text-zinc-500 text-sm mb-4">Créez un compte gratuit pour réserver votre créneau avec Marc.</p>
      <div class="flex gap-3 justify-center">
        <router-link to="/register" class="btn-primary text-sm py-2.5 px-6">Créer un compte</router-link>
        <router-link to="/login" class="btn-ghost text-sm py-2.5 px-6">Se connecter</router-link>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { format, startOfWeek, addWeeks } from 'date-fns'
import { fr } from 'date-fns/locale'
import { useSeancesStore } from '@/stores/seances'
import { useReservationsStore } from '@/stores/reservations'
import { useAuthStore } from '@/stores/auth'
import { useToast } from '@/composables/useToast'
import { useRouter } from 'vue-router'
import LoadingSpinner from '@/components/LoadingSpinner.vue'

const seancesStore = useSeancesStore()
const reservationsStore = useReservationsStore()
const auth = useAuthStore()
const toast = useToast()
const router = useRouter()

const loadingId = ref(null)

// Grouper les séances par semaine puis par jour
const groupedByWeek = computed(() => {
  const weeks = {}

  seancesStore.seances.forEach(s => {
    const d = new Date(s.dateHeure)
    const weekStart = startOfWeek(d, { weekStartsOn: 1 })
    const weekKey = format(weekStart, 'yyyy-MM-dd')
    const dayKey = format(d, 'yyyy-MM-dd')

    if (!weeks[weekKey]) {
      weeks[weekKey] = {
        label: format(weekStart, 'd MMM', { locale: fr }),
        days: {},
      }
    }

    if (!weeks[weekKey].days[dayKey]) {
      weeks[weekKey].days[dayKey] = {
        date: dayKey,
        label: format(d, 'EEEE d MMMM', { locale: fr }),
        seances: [],
        available: 0,
      }
    }

    weeks[weekKey].days[dayKey].seances.push(s)
    if (s.placesRestantes > 0 && !isBooked(s.id)) {
      weeks[weekKey].days[dayKey].available++
    }
  })

  return Object.values(weeks).map(w => ({
    ...w,
    days: Object.values(w.days).sort((a, b) => a.date.localeCompare(b.date)),
  }))
})

function isBooked(seanceId) {
  return reservationsStore.reservations.some(r => r.seance.id === seanceId && r.statut !== 'annulee')
}

function slotClass(seance) {
  if (isBooked(seance.id)) {
    return 'bg-emerald-500/10 border-emerald-500/40 text-emerald-400 cursor-pointer hover:bg-emerald-500/20'
  }
  if (!seance.disponible || seance.placesRestantes === 0) {
    return 'bg-dark-800 border-dark-600 text-zinc-600 cursor-not-allowed opacity-40'
  }
  return 'bg-dark-800 border-dark-600 text-white hover:border-orange-500 hover:bg-orange-500/5 cursor-pointer'
}

async function handleSlotClick(seance) {
  if (!auth.isAuthenticated) {
    router.push('/login')
    return
  }
  if (auth.isAdmin) return
  if (!seance.disponible) return

  if (isBooked(seance.id)) {
    // Annuler
    const res = reservationsStore.reservations.find(r => r.seance.id === seance.id && r.statut !== 'annulee')
    if (!res) return
    loadingId.value = seance.id
    try {
      await reservationsStore.cancelReservation(res.id)
      await reservationsStore.fetchMyReservations()
      await seancesStore.fetchSeances()
      toast.info('Réservation annulée.')
    } catch {
      toast.error('Impossible d\'annuler.')
    } finally {
      loadingId.value = null
    }
    return
  }

  if (seance.placesRestantes === 0) return

  // Réserver
  loadingId.value = seance.id
  try {
    await reservationsStore.bookSeance(seance.id)
    await reservationsStore.fetchMyReservations()
    await seancesStore.fetchSeances()
    toast.success('Créneau réservé avec Marc.')
  } catch (e) {
    toast.error(e.response?.data?.message || 'Impossible de réserver.')
  } finally {
    loadingId.value = null
  }
}

function formatTime(d) { return format(new Date(d), 'HH:mm') }

onMounted(async () => {
  await seancesStore.fetchSeances()
  if (auth.isAuthenticated) await reservationsStore.fetchMyReservations()
})
</script>
