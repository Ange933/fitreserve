<template>
  <article class="glass-card flex flex-col sm:flex-row sm:items-center gap-4 p-5 transition-opacity" :class="{ 'opacity-50': isPast }">
    <!-- Date block -->
    <div class="flex-shrink-0 w-16 text-center border-r border-dark-600 pr-4">
      <p class="font-display font-black text-2xl text-orange-500 leading-none">{{ day }}</p>
      <p class="text-zinc-600 text-xs uppercase tracking-wider mt-0.5">{{ month }}</p>
    </div>

    <!-- Info -->
    <div class="flex-1 min-w-0">
      <div class="flex flex-wrap items-center gap-2 mb-1">
        <h3 class="font-bold text-white uppercase tracking-wide text-sm">{{ reservation.seance.titre }}</h3>
        <span :class="statusBadge.class" class="badge">{{ statusBadge.label }}</span>
      </div>
      <div class="flex flex-wrap gap-x-4 gap-y-0.5 text-xs text-zinc-500">
        <span>{{ time }}</span>
        <span v-if="reservation.seance.lieu">{{ reservation.seance.lieu }}</span>
        <span>{{ reservation.seance.duree }} min</span>
      </div>
      <p class="text-xs text-zinc-700 mt-1.5">Réservé le {{ reservedDate }}</p>
    </div>

    <!-- Action -->
    <div v-if="!isPast && reservation.statut !== 'annulee'" class="flex-shrink-0">
      <button @click="$emit('cancel', reservation.id)" :disabled="loading" class="btn-danger text-xs py-2 px-4">
        <LoadingSpinner v-if="loading" size="sm" />
        <span>Annuler</span>
      </button>
    </div>
  </article>
</template>

<script setup>
import { computed } from 'vue'
import { format } from 'date-fns'
import { fr } from 'date-fns/locale'
import LoadingSpinner from '@/components/LoadingSpinner.vue'

const props = defineProps({
  reservation: { type: Object, required: true },
  loading: { type: Boolean, default: false },
  isPast: { type: Boolean, default: false },
})
defineEmits(['cancel'])

const d = computed(() => new Date(props.reservation.seance.dateHeure))
const day = computed(() => format(d.value, 'd'))
const month = computed(() => format(d.value, 'MMM', { locale: fr }))
const time = computed(() => format(d.value, 'HH:mm'))
const reservedDate = computed(() => format(new Date(props.reservation.createdAt), 'd MMM yyyy', { locale: fr }))

const statusBadge = computed(() => ({
  confirmee: { class: 'badge-green', label: 'Confirmée' },
  annulee: { class: 'badge-red', label: 'Annulée' },
  en_attente: { class: 'badge-yellow', label: 'En attente' },
}[props.reservation.statut] || { class: 'badge-orange', label: props.reservation.statut }))
</script>
