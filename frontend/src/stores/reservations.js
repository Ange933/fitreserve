import { defineStore } from 'pinia'
import { ref } from 'vue'
import { reservationsService } from '@/services/api'

export const useReservationsStore = defineStore('reservations', () => {
  const reservations = ref([])
  const loading = ref(false)
  const error = ref(null)

  async function fetchMyReservations() {
    loading.value = true
    error.value = null
    try {
      const { data } = await reservationsService.myReservations()
      reservations.value = data
    } catch (e) {
      error.value = e.response?.data?.message || 'Erreur lors du chargement.'
    } finally {
      loading.value = false
    }
  }

  async function bookSeance(seanceId) {
    const { data } = await reservationsService.create(seanceId)
    reservations.value.unshift(data)
    return data
  }

  async function cancelReservation(id) {
    await reservationsService.cancel(id)
    const res = reservations.value.find((r) => r.id === id)
    if (res) res.statut = 'annulee'
  }

  return { reservations, loading, error, fetchMyReservations, bookSeance, cancelReservation }
})
