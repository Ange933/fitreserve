import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { authService } from '@/services/api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('jwt_token') || null)
  const loading = ref(false)

  const isAuthenticated = computed(() => !!token.value)
  const isAdmin = computed(() => user.value?.roles?.includes('ROLE_ADMIN') ?? false)
  const fullName = computed(() =>
    user.value ? `${user.value.prenom} ${user.value.nom}` : ''
  )

  async function login(email, password) {
    loading.value = true
    try {
      const { data } = await authService.login(email, password)
      token.value = data.token
      localStorage.setItem('jwt_token', data.token)
      await fetchMe()
      return true
    } finally {
      loading.value = false
    }
  }

  async function register(userData) {
    loading.value = true
    try {
      await authService.register(userData)
      return true
    } finally {
      loading.value = false
    }
  }

  async function fetchMe() {
    if (!token.value) return
    try {
      const { data } = await authService.me()
      user.value = data
    } catch {
      logout()
    }
  }

  function logout() {
    user.value = null
    token.value = null
    localStorage.removeItem('jwt_token')
  }

  if (token.value) {
    fetchMe()
  }

  return { user, token, loading, isAuthenticated, isAdmin, fullName, login, register, fetchMe, logout }
})
