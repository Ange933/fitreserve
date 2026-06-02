<template>
  <div class="page-container max-w-4xl mx-auto">
    <div class="mb-10 animate-fade-in">
      <p class="label-tag text-orange-500 mb-2">Espace membre</p>
      <h1 class="section-title text-white">Mes <span class="text-gradient">réservations</span></h1>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-3 gap-3 mb-10">
      <div v-for="stat in stats" :key="stat.label" class="glass-card p-4 text-center border-t-2 border-orange-500">
        <p class="font-display font-black text-3xl text-white">{{ stat.value }}</p>
        <p class="label-tag mt-1">{{ stat.label }}</p>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="reservationsStore.loading" class="space-y-3">
      <div v-for="i in 3" :key="i" class="skeleton h-20"></div>
    </div>

    <!-- Empty -->
    <div v-else-if="reservationsStore.reservations.length === 0" class="glass-card p-16 text-center">
      <p class="font-display font-black text-white uppercase text-2xl mb-2">Aucune réservation</p>
      <p class="text-zinc-600 text-sm mb-8">Vous n'avez pas encore réservé de séance.</p>
      <router-link to="/seances" class="btn-primary py-3 px-8 inline-flex">Voir les séances</router-link>
    </div>

    <!-- Lists -->
    <div v-else class="space-y-8">
      <div v-if="upcoming.length > 0">
        <p class="label-tag mb-4">A venir ({{ upcoming.length }})</p>
        <div class="space-y-3">
          <ReservationCard
            v-for="res in upcoming"
            :key="res.id"
            :reservation="res"
            :loading="loadingId === res.id"
            @cancel="handleCancel"
          />
        </div>
      </div>

      <div v-if="past.length > 0">
        <button @click="showPast = !showPast" class="label-tag flex items-center gap-2 hover:text-zinc-400 transition-colors mb-4">
          <svg class="w-3 h-3 transition-transform" :class="showPast ? 'rotate-90' : ''" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
          </svg>
          Historique ({{ past.length }})
        </button>
        <transition name="fade">
          <div v-if="showPast" class="space-y-3">
            <ReservationCard v-for="res in past" :key="res.id" :reservation="res" :is-past="true" />
          </div>
        </transition>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useReservationsStore } from '@/stores/reservations'
import { useToast } from '@/composables/useToast'
import ReservationCard from '@/components/ReservationCard.vue'

const reservationsStore = useReservationsStore()
const toast = useToast()
const loadingId = ref(null)
const showPast = ref(false)

const upcoming = computed(() =>
  reservationsStore.reservations.filter((r) => r.statut !== 'annulee' && new Date(r.seance.dateHeure) >= new Date())
)
const past = computed(() =>
  reservationsStore.reservations.filter((r) => r.statut === 'annulee' || new Date(r.seance.dateHeure) < new Date())
)
const stats = computed(() => [
  { value: upcoming.value.length, label: 'A venir' },
  { value: reservationsStore.reservations.filter((r) => r.statut === 'confirmee').length, label: 'Confirmées' },
  { value: reservationsStore.reservations.filter((r) => r.statut === 'annulee').length, label: 'Annulées' },
])

async function handleCancel(id) {
  loadingId.value = id
  try {
    await reservationsStore.cancelReservation(id)
    toast.info('Réservation annulée.')
  } catch {
    toast.error('Impossible d\'annuler.')
  } finally {
    loadingId.value = null
  }
}

onMounted(async () => {
  await reservationsStore.fetchMyReservations()
})
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
