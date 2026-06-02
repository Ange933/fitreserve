<template>
  <article
    class="glass-card-hover flex flex-col relative overflow-hidden"
    :class="{ 'opacity-60': isFull && !booked }"
  >
    <!-- Orange top bar -->
    <div class="h-0.5 w-full" :class="booked ? 'bg-emerald-500' : isFull ? 'bg-red-600' : 'bg-orange-500'"></div>

    <div class="p-5 flex flex-col gap-4 flex-1">
      <!-- Header -->
      <div class="flex items-start justify-between gap-3">
        <div class="flex-1 min-w-0">
          <h3 class="font-display font-black uppercase text-white text-base leading-tight">{{ seance.titre }}</h3>
          <p class="text-zinc-600 text-xs uppercase tracking-wider mt-0.5">{{ seance.lieu || 'Lieu à confirmer' }}</p>
        </div>
        <span :class="statusBadge.class" class="badge flex-shrink-0">{{ statusBadge.label }}</span>
      </div>

      <!-- Infos -->
      <div class="grid grid-cols-3 gap-2">
        <div class="bg-dark-700 p-2 text-center">
          <p class="text-orange-500 font-bold text-sm">{{ formatDate(seance.dateHeure) }}</p>
          <p class="text-zinc-600 text-xs uppercase tracking-wide">Date</p>
        </div>
        <div class="bg-dark-700 p-2 text-center">
          <p class="text-white font-bold text-sm">{{ formatTime(seance.dateHeure) }}</p>
          <p class="text-zinc-600 text-xs uppercase tracking-wide">Heure</p>
        </div>
        <div class="bg-dark-700 p-2 text-center">
          <p class="text-white font-bold text-sm">{{ seance.duree }}<span class="text-zinc-600 text-xs">min</span></p>
          <p class="text-zinc-600 text-xs uppercase tracking-wide">Durée</p>
        </div>
      </div>

      <!-- Disponibilité -->
      <div class="flex items-center gap-2">
        <div class="w-2 h-2 rounded-full flex-shrink-0" :class="isFull ? 'bg-red-500' : 'bg-emerald-500'"></div>
        <span class="text-xs uppercase tracking-wider font-bold" :class="isFull ? 'text-red-400' : 'text-emerald-400'">
          {{ isFull ? 'Créneau pris' : 'Créneau disponible' }}
        </span>
      </div>

      <!-- Actions -->
      <div class="flex gap-2 mt-auto pt-1">
        <template v-if="auth.isAuthenticated && !auth.isAdmin">
          <button
            v-if="!booked"
            @click="$emit('book', seance.id)"
            :disabled="isFull || loading"
            class="btn-primary flex-1 text-xs py-2.5"
            :aria-label="`Réserver ${seance.titre}`"
          >
            <LoadingSpinner v-if="loading" size="sm" />
            <span>{{ isFull ? 'Complet' : 'Réserver' }}</span>
          </button>
          <button
            v-else
            @click="$emit('cancel', reservationId)"
            :disabled="loading"
            class="btn-danger flex-1 text-xs py-2.5"
          >
            <LoadingSpinner v-if="loading" size="sm" />
            <span>Annuler</span>
          </button>
        </template>

        <template v-if="auth.isAdmin">
          <button @click="$emit('edit', seance)" class="btn-ghost flex-1 text-xs py-2.5">Modifier</button>
          <button
            @click="$emit('delete', seance.id)"
            class="w-10 h-10 flex items-center justify-center text-red-500 hover:bg-red-500/10 transition-colors border border-dark-600"
            aria-label="Supprimer"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
          </button>
        </template>

        <router-link
          v-if="!auth.isAuthenticated"
          to="/login"
          class="btn-primary flex-1 text-xs py-2.5 text-center"
        >
          Se connecter pour réserver
        </router-link>
      </div>
    </div>
  </article>
</template>

<script setup>
import { computed } from 'vue'
import { format } from 'date-fns'
import { fr } from 'date-fns/locale'
import { useAuthStore } from '@/stores/auth'
import LoadingSpinner from '@/components/LoadingSpinner.vue'

const props = defineProps({
  seance: { type: Object, required: true },
  booked: { type: Boolean, default: false },
  reservationId: { type: String, default: null },
  loading: { type: Boolean, default: false },
})
defineEmits(['book', 'cancel', 'edit', 'delete'])

const auth = useAuthStore()

const isFull = computed(() => props.seance.placesRestantes <= 0)
const fillPercent = computed(() =>
  Math.round(((props.seance.placesMax - props.seance.placesRestantes) / props.seance.placesMax) * 100)
)

const progressColor = computed(() => {
  if (fillPercent.value >= 90) return 'bg-red-500'
  if (fillPercent.value >= 60) return 'bg-amber-500'
  return 'bg-orange-500'
})

const statusBadge = computed(() => {
  if (props.booked) return { class: 'badge-green', label: 'Réservé' }
  if (isFull.value) return { class: 'badge-red', label: 'Complet' }
  return { class: 'badge-orange', label: 'Disponible' }
})

function formatDate(d) { return format(new Date(d), 'dd MMM', { locale: fr }) }
function formatTime(d) { return format(new Date(d), 'HH:mm') }
</script>
