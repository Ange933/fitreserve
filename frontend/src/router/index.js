import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [
  {
    path: '/',
    name: 'home',
    component: () => import('@/views/HomeView.vue'),
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('@/views/LoginView.vue'),
    meta: { guest: true },
  },
  {
    path: '/register',
    name: 'register',
    component: () => import('@/views/RegisterView.vue'),
    meta: { guest: true },
  },
  {
    path: '/seances',
    name: 'seances',
    component: () => import('@/views/SeancesView.vue'),
  },
  {
    path: '/reservations',
    name: 'reservations',
    component: () => import('@/views/ReservationView.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/profil',
    name: 'profil',
    component: () => import('@/views/ProfilView.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/admin',
    name: 'admin',
    component: () => import('@/views/AdminView.vue'),
    meta: { requiresAdmin: true },
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    return savedPosition || { top: 0 }
  },
})

router.beforeEach((to, from, next) => {
  const auth = useAuthStore()

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return next({ name: 'login', query: { redirect: to.fullPath } })
  }
  if (to.meta.requiresAdmin && !auth.isAdmin) {
    return next({ name: 'home' })
  }
  if (to.meta.guest && auth.isAuthenticated) {
    return next({ name: 'seances' })
  }
  next()
})

export default router
