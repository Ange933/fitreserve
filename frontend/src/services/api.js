import axios from 'axios'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || '',
  withCredentials: true,
  headers: { 'Content-Type': 'application/json' },
})

api.interceptors.request.use((config) => {
  const token = localStorage.getItem('jwt_token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('jwt_token')
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

export const authService = {
  register: (data) => api.post('/api/register', data),
  login: (email, password) => api.post('/api/login_check', { username: email, password }),
  me: () => api.get('/api/me'),
  updateMe: (data) => api.put('/api/me', data),
  adminGetUser: (id) => api.get(`/api/admin/users/${id}`),
}

export const seancesService = {
  list: () => api.get('/api/seances'),
  adminList: () => api.get('/api/seances/admin/all'),
  get: (id) => api.get(`/api/seances/${id}`),
  create: (data) => api.post('/api/seances', data),
  update: (id, data) => api.put(`/api/seances/${id}`, data),
  delete: (id) => api.delete(`/api/seances/${id}`),
  toggleDisponibilite: (id) => api.patch(`/api/seances/${id}/disponibilite`),
}

export const reservationsService = {
  myReservations: () => api.get('/api/reservations'),
  create: (seanceId) => api.post('/api/reservations', { seanceId }),
  cancel: (id) => api.delete(`/api/reservations/${id}`),
  allReservations: () => api.get('/api/admin/reservations'),
  allUsers: () => api.get('/api/admin/users'),
}

export default api
