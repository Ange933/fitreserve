import { defineStore } from 'pinia'
import { ref } from 'vue'
import { seancesService } from '@/services/api'

export const useSeancesStore = defineStore('seances', () => {
  const seances = ref([])
  const loading = ref(false)
  const error = ref(null)

  async function fetchSeances(adminMode = false) {
    loading.value = true
    error.value = null
    try {
      const { data } = adminMode ? await seancesService.adminList() : await seancesService.list()
      seances.value = data
    } catch (e) {
      error.value = e.response?.data?.message || 'Erreur lors du chargement.'
    } finally {
      loading.value = false
    }
  }

  async function toggleDisponibilite(id) {
    const { data } = await seancesService.toggleDisponibilite(id)
    const index = seances.value.findIndex((s) => s.id === id)
    if (index !== -1) seances.value[index] = data
    return data
  }

  async function createSeance(data) {
    const { data: seance } = await seancesService.create(data)
    seances.value.push(seance)
    return seance
  }

  async function updateSeance(id, data) {
    const { data: updated } = await seancesService.update(id, data)
    const index = seances.value.findIndex((s) => s.id === id)
    if (index !== -1) seances.value[index] = updated
    return updated
  }

  async function deleteSeance(id) {
    await seancesService.delete(id)
    seances.value = seances.value.filter((s) => s.id !== id)
  }

  return { seances, loading, error, fetchSeances, createSeance, updateSeance, deleteSeance, toggleDisponibilite }
})
