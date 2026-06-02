import { describe, it, expect, vi, beforeEach } from 'vitest'
import { setActivePinia, createPinia } from 'pinia'
import { useAuthStore } from '@/stores/auth'

vi.mock('@/services/api', () => ({
  authService: {
    login: vi.fn().mockResolvedValue({ data: { token: 'fake-jwt-token' } }),
    me: vi.fn().mockResolvedValue({
      data: { id: '1', email: 'test@test.fr', nom: 'Test', prenom: 'User', roles: ['ROLE_USER'] },
    }),
    register: vi.fn().mockResolvedValue({ data: { message: 'OK' } }),
  },
}))

describe('AuthStore', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    localStorage.clear()
  })

  it('starts unauthenticated', () => {
    const auth = useAuthStore()
    expect(auth.isAuthenticated).toBe(false)
    expect(auth.user).toBeNull()
  })

  it('sets token on login', async () => {
    const auth = useAuthStore()
    await auth.login('test@test.fr', 'Password1')
    expect(auth.token).toBe('fake-jwt-token')
    expect(auth.isAuthenticated).toBe(true)
  })

  it('clears state on logout', async () => {
    const auth = useAuthStore()
    await auth.login('test@test.fr', 'Password1')
    auth.logout()
    expect(auth.isAuthenticated).toBe(false)
    expect(auth.user).toBeNull()
    expect(localStorage.getItem('jwt_token')).toBeNull()
  })

  it('detects admin role correctly', async () => {
    const { authService } = await import('@/services/api')
    authService.me.mockResolvedValueOnce({
      data: { id: '2', email: 'admin@test.fr', nom: 'Admin', prenom: 'User', roles: ['ROLE_ADMIN', 'ROLE_USER'] },
    })
    const auth = useAuthStore()
    await auth.login('admin@test.fr', 'Admin1')
    expect(auth.isAdmin).toBe(true)
  })
})
