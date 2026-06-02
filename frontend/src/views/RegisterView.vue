<template>
  <div class="min-h-screen flex">
    <!-- Photo side -->
    <div class="hidden lg:block lg:w-1/2 relative">
      <img src="/images/pullups.jpg" alt="Performance sportive" class="w-full h-full object-cover object-top" />
      <div class="absolute inset-0 bg-dark-950/60"></div>
      <div class="absolute bottom-12 left-12">
        <p class="font-display font-black text-white uppercase text-5xl leading-none">
          La discipline<br />forge les<br /><span class="text-orange-500">champions.</span>
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
          <h1 class="text-xl font-bold text-white">Créer un compte</h1>
          <p class="text-zinc-500 text-sm mt-1">Rejoignez la communauté FitReserve</p>
        </div>

        <form @submit.prevent="handleRegister" class="space-y-4" novalidate>
          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-1.5">
              <label for="prenom" class="label-tag">Prénom</label>
              <input id="prenom" v-model="form.prenom" type="text" autocomplete="given-name" placeholder="Sophie" class="input-field" :class="{ 'border-red-500': errors.prenom }" required aria-required="true" aria-describedby="prenom-error" />
              <p id="prenom-error" v-if="errors.prenom" class="text-red-400 text-xs font-medium" role="alert">{{ errors.prenom }}</p>
            </div>
            <div class="space-y-1.5">
              <label for="nom" class="label-tag">Nom</label>
              <input id="nom" v-model="form.nom" type="text" autocomplete="family-name" placeholder="Martin" class="input-field" :class="{ 'border-red-500': errors.nom }" required aria-required="true" aria-describedby="nom-error" />
              <p id="nom-error" v-if="errors.nom" class="text-red-400 text-xs font-medium" role="alert">{{ errors.nom }}</p>
            </div>
          </div>

          <div class="space-y-1.5">
            <label for="email" class="label-tag">Adresse email</label>
            <input id="email" v-model="form.email" type="email" autocomplete="email" placeholder="vous@exemple.fr" class="input-field" :class="{ 'border-red-500': errors.email }" required aria-required="true" aria-describedby="email-error" />
            <p id="email-error" v-if="errors.email" class="text-red-400 text-xs font-medium" role="alert">{{ errors.email }}</p>
          </div>

          <div class="space-y-1.5">
            <label for="telephone" class="label-tag">Numéro de téléphone</label>
            <input id="telephone" v-model="form.telephone" type="tel" autocomplete="tel" placeholder="06 12 34 56 78" class="input-field" :class="{ 'border-red-500': errors.telephone }" aria-describedby="telephone-error" />
            <p id="telephone-error" v-if="errors.telephone" class="text-red-400 text-xs font-medium" role="alert">{{ errors.telephone }}</p>
          </div>

          <div class="space-y-1.5">
            <label for="password" class="label-tag">Mot de passe</label>
            <div class="relative">
              <input id="password" v-model="form.password" :type="showPwd ? 'text' : 'password'" autocomplete="new-password" placeholder="Min. 8 car., 1 majuscule, 1 chiffre" class="input-field pr-12" :class="{ 'border-red-500': errors.password }" required aria-required="true" aria-describedby="password-error" />
              <button type="button" @click="showPwd = !showPwd" class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-600 hover:text-zinc-300 transition-colors" :aria-label="showPwd ? 'Masquer' : 'Afficher'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
              </button>
            </div>
            <div class="flex gap-1 mt-1.5" aria-hidden="true">
              <div v-for="i in 4" :key="i" class="h-0.5 flex-1 transition-all duration-300" :class="i <= strength ? strengthColor : 'bg-dark-600'"></div>
            </div>
            <p id="password-error" v-if="errors.password" class="text-red-400 text-xs font-medium" role="alert">{{ errors.password }}</p>
          </div>

          <div class="space-y-1.5">
            <label for="confirmPassword" class="label-tag">Confirmer le mot de passe</label>
            <div class="relative">
              <input id="confirmPassword" v-model="form.confirmPassword" :type="showConfirmPwd ? 'text' : 'password'" autocomplete="new-password" placeholder="••••••••" class="input-field pr-12" :class="{ 'border-red-500': errors.confirmPassword }" required aria-required="true" aria-describedby="confirmPassword-error" />
              <button type="button" @click="showConfirmPwd = !showConfirmPwd" :aria-label="showConfirmPwd ? 'Masquer la confirmation' : 'Afficher la confirmation'" class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-600 hover:text-zinc-300 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
              </button>
            </div>
            <p id="confirmPassword-error" v-if="errors.confirmPassword" class="text-red-400 text-xs font-medium" role="alert">{{ errors.confirmPassword }}</p>
          </div>

          <div v-if="errors.global" class="p-4 border-l-2 border-red-500 bg-red-500/5 text-red-300 text-sm font-medium" role="alert" aria-live="assertive">
            {{ errors.global }}
          </div>

          <button type="submit" class="btn-primary w-full py-4 text-sm" :disabled="loading">
            <LoadingSpinner v-if="loading" size="sm" />
            <span>{{ loading ? 'Création...' : 'Créer mon compte' }}</span>
          </button>
        </form>

        <p class="text-center text-sm text-zinc-600 mt-8">
          Déjà un compte ?
          <router-link to="/login" class="text-orange-500 hover:text-orange-400 font-bold transition-colors">Se connecter</router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useToast } from '@/composables/useToast'
import LoadingSpinner from '@/components/LoadingSpinner.vue'

const auth = useAuthStore()
const router = useRouter()
const toast = useToast()

const showPwd = ref(false)
const showConfirmPwd = ref(false)
const loading = ref(false)
const form = reactive({ prenom: '', nom: '', email: '', telephone: '', password: '', confirmPassword: '' })
const errors = reactive({ prenom: '', nom: '', email: '', telephone: '', password: '', confirmPassword: '', global: '' })

const strength = computed(() => {
  const p = form.password
  return [p.length >= 8, /[A-Z]/.test(p), /\d/.test(p), /[^A-Za-z0-9]/.test(p)].filter(Boolean).length
})
const strengthColor = computed(() => ['bg-red-500', 'bg-red-500', 'bg-amber-500', 'bg-lime-500', 'bg-orange-500'][strength.value])

function validate() {
  Object.keys(errors).forEach((k) => (errors[k] = ''))
  let valid = true
  if (!form.prenom.trim()) { errors.prenom = 'Requis.'; valid = false }
  if (!form.nom.trim()) { errors.nom = 'Requis.'; valid = false }
  if (!form.email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) { errors.email = 'Email invalide.'; valid = false }
  if (form.password.length < 8) { errors.password = 'Minimum 8 caractères.'; valid = false }
  else if (!/^(?=.*[A-Z])(?=.*\d).+$/.test(form.password)) { errors.password = 'Doit contenir une majuscule et un chiffre.'; valid = false }
  if (form.password && form.confirmPassword && form.password !== form.confirmPassword) {
    errors.confirmPassword = 'Les mots de passe ne correspondent pas.'; valid = false
  }
  if (form.telephone && !/^(\+33|0)[1-9](\d{2}){4}$/.test(form.telephone.replace(/\s/g, ''))) {
    errors.telephone = 'Numéro invalide (ex: 06 12 34 56 78).'; valid = false
  }
  return valid
}

async function handleRegister() {
  if (!validate()) return
  loading.value = true
  try {
    await auth.register({ email: form.email, password: form.password, nom: form.nom, prenom: form.prenom, telephone: form.telephone || undefined })
    toast.success('Compte créé. Connectez-vous.')
    router.push('/login')
  } catch (e) {
    const data = e.response?.data
    if (data?.errors) Object.assign(errors, data.errors)
    else errors.global = data?.message || 'Une erreur est survenue.'
  } finally {
    loading.value = false
  }
}
</script>
