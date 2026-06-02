<template>
  <div class="min-h-screen flex">
    <!-- Photo side -->
    <div class="hidden lg:block lg:w-1/2 relative">
      <img src="/images/cardio.jpg" alt="Salle de sport" class="w-full h-full object-cover" />
      <div class="absolute inset-0 bg-dark-950/60"></div>
      <div class="absolute bottom-12 left-12">
        <p class="font-display font-black text-white uppercase text-5xl leading-none">
          Chaque jour<br />est un<br /><span class="text-orange-500">nouveau défi.</span>
        </p>
      </div>
    </div>

    <!-- Form side -->
    <div class="w-full lg:w-1/2 flex items-center justify-center px-4 py-24 bg-dark-950">
      <div class="w-full max-w-md animate-slide-up">

        <div class="mb-10">
          <div class="flex items-center gap-2 mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" class="w-8 h-8 flex-shrink-0">
              <rect width="100" height="100" rx="22" fill="#111111"/>
              <polygon points="62,6 30,52 52,52 38,94 70,48 48,48" fill="#f97316"/>
            </svg>
            <div class="flex items-center gap-0.5">
              <span class="font-display font-black text-2xl uppercase tracking-tighter text-white">FIT</span>
              <span class="font-display font-black text-2xl uppercase tracking-tighter text-orange-500">RESERVE</span>
            </div>
          </div>
          <h1 class="text-xl font-bold text-white">Connexion</h1>
          <p class="text-zinc-500 text-sm mt-1">Accédez à votre espace personnel</p>
        </div>

        <form @submit.prevent="handleLogin" class="space-y-5" novalidate>

          <div class="space-y-1.5">
            <label for="email" class="label-tag">Adresse email</label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              autocomplete="email"
              placeholder="vous@exemple.fr"
              class="input-field"
              :class="{ 'border-red-500': errors.email }"
              required
              aria-required="true"
              aria-describedby="email-error"
            />
            <p id="email-error" v-if="errors.email" class="text-red-400 text-xs font-medium" role="alert">{{ errors.email }}</p>
          </div>

          <div class="space-y-1.5">
            <label for="password" class="label-tag">Mot de passe</label>
            <div class="relative">
              <input
                id="password"
                v-model="form.password"
                :type="showPwd ? 'text' : 'password'"
                autocomplete="current-password"
                placeholder="••••••••"
                class="input-field pr-12"
                :class="{ 'border-red-500': errors.password }"
                required
                aria-required="true"
                aria-describedby="password-error"
              />
              <button type="button" @click="showPwd = !showPwd" class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-600 hover:text-zinc-300 transition-colors" :aria-label="showPwd ? 'Masquer' : 'Afficher'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path v-if="!showPwd" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                </svg>
              </button>
            </div>
            <p id="password-error" v-if="errors.password" class="text-red-400 text-xs font-medium" role="alert">{{ errors.password }}</p>
          </div>

          <div v-if="errors.global" class="p-4 border-l-2 border-red-500 bg-red-500/5 text-red-300 text-sm font-medium" role="alert">
            {{ errors.global }}
          </div>

          <button type="submit" class="btn-primary w-full py-4 text-sm" :disabled="auth.loading">
            <LoadingSpinner v-if="auth.loading" size="sm" />
            <span>{{ auth.loading ? 'Connexion...' : 'Se connecter' }}</span>
          </button>

          <button type="button" @click="showForgot = true" class="w-full text-center text-sm text-zinc-500 hover:text-orange-500 transition-colors font-medium pt-1">
            Mot de passe oublié ?
          </button>
        </form>

        <p class="text-center text-sm text-zinc-600 mt-6">
          Pas encore de compte ?
          <router-link to="/register" class="text-orange-500 hover:text-orange-400 font-bold transition-colors">Créer un compte</router-link>
        </p>

        <div v-if="isDev" class="mt-6 p-4 border border-dark-600 bg-dark-800/50 space-y-2">
          <p class="label-tag text-zinc-600">Accès démo</p>
          <button type="button" @click="fillDemo('admin@fitreserve.fr', 'Admin1234!')" class="w-full text-left text-xs text-zinc-500 hover:text-zinc-300 transition-colors py-1 font-medium">
            Admin : admin@fitreserve.fr / Admin1234!
          </button>
          <button type="button" @click="fillDemo('sophie.martin@email.fr', 'User1234!')" class="w-full text-left text-xs text-zinc-500 hover:text-zinc-300 transition-colors py-1 font-medium">
            Client : sophie.martin@email.fr / User1234!
          </button>
        </div>
      </div>
    </div>

    <!-- Modal mot de passe oublié -->
    <Modal v-model="showForgot" title="Mot de passe oublié">
      <div class="space-y-5">
        <p class="text-zinc-400 text-sm leading-relaxed">
          Contactez Marc directement pour réinitialiser votre mot de passe.
        </p>
        <div class="p-4 bg-dark-800 border border-dark-600 space-y-3">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-orange-500/20 border border-orange-500/30 flex items-center justify-center font-bold text-orange-500 text-xs flex-shrink-0">MD</div>
            <div>
              <p class="font-bold text-white text-sm">Marc Durand</p>
              <p class="text-zinc-500 text-xs">Coach Sportif — Paris 8e</p>
            </div>
          </div>
          <a href="mailto:admin@fitreserve.fr" class="block text-orange-500 hover:text-orange-400 transition-colors text-sm font-medium">
            admin@fitreserve.fr
          </a>
        </div>
        <button @click="showForgot = false" class="btn-ghost w-full py-3 text-sm">Fermer</button>
      </div>
    </Modal>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useToast } from '@/composables/useToast'
import LoadingSpinner from '@/components/LoadingSpinner.vue'
import Modal from '@/components/Modal.vue'

const auth = useAuthStore()
const router = useRouter()
const route = useRoute()
const toast = useToast()

const isDev = import.meta.env.DEV
const showPwd = ref(false)
const showForgot = ref(false)
const form = reactive({ email: '', password: '' })
const errors = reactive({ email: '', password: '', global: '' })

function validate() {
  Object.keys(errors).forEach(k => errors[k] = '')
  let valid = true
  if (!form.email) { errors.email = 'L\'email est requis.'; valid = false }
  if (!form.password) { errors.password = 'Le mot de passe est requis.'; valid = false }
  return valid
}

async function handleLogin() {
  if (!validate()) return
  try {
    await auth.login(form.email, form.password)
    toast.success('Connexion réussie.')
    router.push(route.query.redirect || '/seances')
  } catch (e) {
    errors.global = e.response?.data?.message || 'Email ou mot de passe incorrect.'
  }
}

function fillDemo(email, password) {
  form.email = email
  form.password = password
}
</script>
